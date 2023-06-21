<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CablePathController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\TrunkController;

use App\Models\CablePathModel;
use App\Models\LocationModel;
use App\Models\LocationModelNoImgData;
use App\Models\ObjectModel;
use App\Models\TemplateModel;
use App\Models\TrunkModel;
use App\Models\ConnectionModel;
use App\Models\OrganizationModel;

class PCM extends Controller
{
	
	// Properites
	public $defaultPortFormatField = array(
		"type" => "static",
		"value" => "Port",
		"count" => 0,
		"order" => 0
	);

    /**
     * Return list of occupied RUs
     *
     * @param  int  $locationID
     * @return arr
     */
    public function generateOccupiedRUArray($locationID)
    {
        
        // Initialize some variables
        $occupiedRUArray = array('front' => [], 'rear' => []);
        $location = LocationModel::where('id', $locationID)->first();
        $locationRUOrientation = $location['ru_orientation'];

        // Populate occupied RU array
        $cabinetObjects = ObjectModel::where('location_id', $locationID)->get();
        foreach($cabinetObjects as $cabinetObject) {
            $cabinetObjectID = $cabinetObject['id'];
            $cabinetObjectTemplateID = $cabinetObject['template_id'];
            $cabinetObjectCabinetFront = $cabinetObject['cabinet_front'];
            $cabinetObjectCabinetRU = $cabinetObject['cabinet_ru'];
            $cabinetObjectTemplate = TemplateModel::where('id', $cabinetObjectTemplateID)->first();
            $cabinetObjectMountConfig = $cabinetObjectTemplate['mount_config'];
            $cabinetObjectRUSize = $cabinetObjectTemplate['ru_size'];
            for($x=0; $x<$cabinetObjectRUSize; $x++) {
                $ruPosition = $cabinetObjectCabinetRU + $x;
                if($cabinetObjectMountConfig == '4-post') {
                    $occupiedRUArray['front'][$ruPosition] = $cabinetObjectID;
                    $occupiedRUArray['rear'][$ruPosition] = $cabinetObjectID;
                } else {
                    $occupiedRUArray[$cabinetObjectCabinetFront][$ruPosition] = $cabinetObjectID;
                }
            }
        }

        return $occupiedRUArray;
    }

    /**
     * Return RU index
     *
     * @param  int  $cabinetRU
     * @param  int  $cabinetSize
     * @param  int  $cabinetOrientation
     * @return int
     */
    public function getRUIndex($cabinetRU, $cabinetSize, $cabinetOrientation)
    {

        $RUIndex = $cabinetRU;
        if($cabinetOrientation == 'bottom-up') {
            $RUIndex = ($cabinetSize - $cabinetRU) + 1;
        }

        return $RUIndex;
    }

    /**
     * Validate cabinet resize
     *
     * @param  int  $locationID
	 * @param  int  $templateID
	 * @param  int  $cabinetRU
     * @return arr
     */
    public function validateCabinetResize($locationID, $newSize)
    {

        // Initialize some variables
        $occupiedRUArray = $this->generateOccupiedRUArray($locationID);

        $cabinet = LocationModel::where('id', $locationID)->first();
        $cabinetSize = $cabinet->size;
        $cabinetOrientation = $cabinet->ru_orientation;

        if($newSize < $cabinetSize) {
            $sizeDiff = $cabinetSize - $newSize;
            if($cabinetOrientation == 'bottom-up') {
                for($x=1; $x<=$sizeDiff; $x++) {
                    if(in_array($x, array_keys($occupiedRUArray['front'])) || in_array($x, array_keys($occupiedRUArray['rear']))) {
                        return ['cabinet_ru' => 'Cabinet cannot be smaller than numerically highest RU occupied by an object.'];
                    }
                }
            } else {
                for($x=$cabinetSize; $x>$newSize; $x--) {
                    if(in_array($x, array_keys($occupiedRUArray['front'])) || in_array($x, array_keys($occupiedRUArray['rear']))) {
                        return ['cabinet_ru' => 'Cabinet cannot be smaller than numerically highest RU occupied by an object.'];
                    }
                }
            }
        }

        return true;
    }

	/**
     * Validate parent partition
     *
     * @param  int  $parentObjectTemplateID
	 * @param  str  $objectParentFace
	 * @param  arr  $objectParentPartitionAddress
	 * @param  arr  $objectParentEnclosureAddress
     * @return arr
     */
    public function validateParentPartition($parentObjectTemplateID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress)
    {
        // Validate parent object partition
        $parentObjectTemplate = TemplateModel::where('id', $parentObjectTemplateID)->first();
        $parentObjectPartition = $this->getPartition($parentObjectTemplate['blueprint'], $objectParentFace, $objectParentPartitionAddress);
        if($parentObjectPartition) {
            if($parentObjectPartition['type'] == 'enclosure') {
                if($objectParentEnclosureAddress[0] >= $parentObjectPartition['enc_layout']['rows'] || $objectParentEnclosureAddress[1] >= $parentObjectPartition['enc_layout']['cols']) {
                    return['parentPartitionAddress' => 'Destination partition address is invalid.'];
                }
            } else {
                return['parentPartitionAddress' => 'Destination partition type is not an enclosure.'];
            }
        } else {
            return['parentPartitionAddress' => 'Destination partition cannot be found.'];
        }
		
		return true;
    }

	/**
     * Validate enclosure occupancy
     *
     * @param  int  $objectParentID
	 * @param  str  $objectParentFace
	 * @param  arr  $objectParentPartitionAddress
	 * @param  arr  $objectParentEnclosureAddress
     * @return arr
     */
    public function validateEnclosureOccupancy($objectID, $objectParentID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress)
    {
        // Validate parent object enclosure occupancy
        $insertsFound = ObjectModel::where(['parent_id' => $objectParentID, 'parent_face' => $objectParentFace])->get();
        foreach($insertsFound as $insert) {
            if($insert['parent_partition_address'] == $objectParentPartitionAddress && $insert['parent_enclosure_address'] == $objectParentEnclosureAddress && $insert['id'] != $objectID) {
                return ['parentEnclosureAddress' => 'Destination enclosure slot is occupied.'];
            }
        }
		
		return true;
    }

    /**
     * Validate connection path
     *
     * @param  arr  $portA
	 * @param  arr  $portB
     * @return boolean
     */
    public function validateConnectionPath($portA, $portB, $archiveAddress)
    {
        $visitedArray = array();
        $portArray = array($portA, $portB);

        $debugArray = array();
        foreach($portArray as $index => $port) {
            if($port['id']) {
                $debugObject = ObjectModel::where('id', $port['id'])->first();
                $debugObjectTemplateID = $debugObject['template_id'];
                $debugLocationID = $debugObject['location_id'];
                $debugLocation = LocationModel::where('id', $debugLocationID)->first();
                $debugLocationArray = array($debugLocation['name'], $debugObject['name']);
                $debugLocationParentID = $debugLocation['parent_id'];
                while($debugLocationParentID != 0) {
                    $debugLocationParent = LocationModel::where('id', $debugLocationParentID)->first();
                    array_unshift($debugLocationArray, $debugLocationParent['name']);
                    $debugLocationParentID = $debugLocationParent['parent_id'];
                }
                $debugObjectFace = $port['face'];
                $debugObjectPartition = json_encode($port['partition']);
                $debugObjectPortID = $port['port_id'];
                $debugObjectTemplate = TemplateModel::where('id', $debugObjectTemplateID)->first();
                array_push($debugArray, implode('.', $debugLocationArray).' - '.$debugObjectFace.' - '.$debugObjectPartition.' - '.$debugObjectPortID);
            } else {
                array_push($debugArray, 'Empy Port Data');
            }
        }

        foreach($portArray as $index => $port) {

            $workingPort = $port;

            while($workingPort['id']) {

                // Generate port hash
                $portHashArray = array(
                    $workingPort['id'],
                    $port['face'],
                    json_encode($port['partition']),
                    $port['port_id']
                );
                $portHash = md5(implode('-', $portHashArray));

                // if($debug) {
                //     $debugObject = ObjectModel::where('id', $portHashArray[0])->first();
                //     $debugObjectTemplateID = $debugObject['template_id'];
                //     $debugObjectFace = $portHashArray[1];
                //     $debugObjectPartition = $portHashArray[2];
                //     $debugObjectPortID = $portHashArray[3];
                //     $debugObjectTemplate = TemplateModel::where('id', $debugObjectTemplateID)->first();
                //     Log::info($debugObject['name'].' - '.$debugObjectTemplate['name'].' - '.$debugObjectFace.' - '.$debugObjectPartition.' - '.$debugObjectPortID);
                // }

                // Generate port hash
                //$portHash = md5(implode('-', array($workingPort['id'], $port['face'], json_encode($port['partition']), $port['port_id'])));
                
                // Check to see if port has been visited
                if(in_array($portHash, $visitedArray)) {
                    Log::info($archiveAddress);
                    foreach($debugArray as $debugArrayEntry) {
                        Log::info($debugArrayEntry);
                    }
                    return false;
                }

                // Add port hash to array of visited ports
                array_push($visitedArray, $portHash);

                // Get template function
                $object = ObjectModel::where('id', $workingPort['id'])->first();
                if($object['floorplan_object_type']) {
                    $templateFunction = 'floorplan';
                } else {
                    $templateID = $object['template_id'];
                    $template = TemplateModel::where('id', $templateID)->first();
                    if($archiveAddress == 'connection.csv:333') {
                        Log::info('$templateID: '.$object);
                        Log::info('$templateID: '.$templateID);
                        Log::info($template);
                    }
                    $templateFunction = $template['function'];
                }

                if($templateFunction == 'passive') {

                    // Get trunk
                    $trunks = TrunkModel::where([
                        ['a_id', '=', $workingPort['id']],
                        ['a_face', '=', $workingPort['face']],
                        ['a_partition', '=', $workingPort['partition']]
                    ])->orwhere([
                        ['b_id', '=', $workingPort['id']],
                        ['b_face', '=', $workingPort['face']],
                        ['b_partition', '=', $workingPort['partition']]
                    ])->get();

                    $trunkFound = false;
                    foreach($trunks as $trunk) {

                        // Determine remote and local side
                        $localSide = ($trunk['a_id'] == $workingPort['id']) ? 'a' : 'b';
                        $remoteSide = ($trunk['a_id'] == $workingPort['id']) ? 'b' : 'a';

                        // Determine if trunk entry is relevant to this cable path
                        if($trunk[$localSide.'_port'] == null && $trunk[$remoteSide.'_port'] == null) {

                            // Both sides are null which means both sides are cabinet objects
                            $trunkFound = true;

                        } else if($trunk[$localSide.'_port'] == null) {

                            // Local side is null which means it is a floorplan object
                            if($trunk[$remoteSide.'_port'] == $workingPort['port_id']) {

                                // Remote port matches so this entry is relevant to the cable path
                                $trunkFound = true;
                            }
                        } else if($trunk[$remoteSide.'_port'] == null) {

                            // Remote side is null which means it is a floorplan object
                            if($trunk[$localSide.'_port'] == $workingPort['port_id']) {

                                // Local port matches so this entry is relevant to the cable path
                                $trunkFound = true;
                            }
                        }
                    }

                    if($trunkFound) {

                        // Store remote object data
                        $workingPort['id'] = $trunk[$remoteSide.'_id'];
                        $workingPort['face'] = $trunk[$remoteSide.'_face'];
                        $workingPort['partition'] = $trunk[$remoteSide.'_partition'];

                        // Generate port hash
                        $portHashArray = array(
                            $workingPort['id'],
                            $workingPort['face'],
                            json_encode($workingPort['partition']),
                            $workingPort['port_id']
                        );
                        $portHash = md5(implode('-', $portHashArray));

                        // if($debug) {
                        //     Log::info('===Trunk===');
                        //     $debugObject = ObjectModel::where('id', $portHashArray[0])->first();
                        //     $debugObjectTemplateID = $debugObject['template_id'];
                        //     $debugObjectFace = $portHashArray[1];
                        //     $debugObjectPartition = $portHashArray[2];
                        //     $debugObjectPortID = $portHashArray[3];
                        //     $debugObjectTemplate = TemplateModel::where('id', $debugObjectTemplateID)->first();
                        //     Log::info($debugObject['name'].' - '.$debugObjectTemplate['name'].' - '.$debugObjectFace.' - '.$debugObjectPartition.' - '.$debugObjectPortID);
                        // }
                        
                        // Check to see if port has been visited
                        if(in_array($portHash, $visitedArray)) {
                            Log::info($archiveAddress);
                            foreach($debugArray as $debugArrayEntry) {
                                Log::info($debugArrayEntry);
                            }
                            return false;
                        }

                        // Add port hash to array of visited ports
                        array_push($visitedArray, $portHash);

                        // Get connection
                        $connection = ConnectionModel::where([
                            ['a_id', '=', $workingPort['id']],
                            ['a_face', '=', $workingPort['face']],
                            ['a_partition', '=', $workingPort['partition']],
                            ['a_port', '=', $workingPort['port_id']]
                        ])->orwhere([
                            ['b_id', '=', $workingPort['id']],
                            ['b_face', '=', $workingPort['face']],
                            ['b_partition', '=', $workingPort['partition']],
                            ['b_port', '=', $workingPort['port_id']]
                        ])->first();

                        if($connection) {

                            // Determine remote and local side
                            $localSide = ($connection['a_id'] == $workingPort['id']) ? 'a' : 'b';
                            $remoteSide = ($connection['a_id'] == $workingPort['id']) ? 'b' : 'a';

                            if($connection[$remoteSide.'_id']) {

                                // Store remote object data
                                $workingPort['id'] = $connection[$remoteSide.'_id'];
                                $workingPort['face'] = $connection[$remoteSide.'_face'];
                                $workingPort['partition'] = $connection[$remoteSide.'_partition'];
                                $workingPort['port_id'] = $connection[$remoteSide.'port'];

                                // Generate port hash
                                $portHashArray = array(
                                    $workingPort['id'],
                                    $workingPort['face'],
                                    json_encode($workingPort['partition']),
                                    $workingPort['port_id']
                                );
                                $portHash = md5(implode('-', $portHashArray));

                                // if($debug) {
                                //     Log::info('---Connection---');
                                //     $debugObject = ObjectModel::where('id', $portHashArray[0])->first();
                                //     $debugObjectTemplateID = $debugObject['template_id'];
                                //     $debugObjectFace = $portHashArray[1];
                                //     $debugObjectPartition = $portHashArray[2];
                                //     $debugObjectPortID = $portHashArray[3];
                                //     $debugObjectTemplate = TemplateModel::where('id', $debugObjectTemplateID)->first();
                                //     Log::info($debugObject['name'].' - '.$debugObjectTemplate['name'].' - '.$debugObjectFace.' - '.$debugObjectPartition.' - '.$debugObjectPortID);
                                // }
                                
                                // Check to see if port has been visited
                                if(in_array($portHash, $visitedArray)) {
                                    Log::info($archiveAddress);
                                    foreach($debugArray as $debugArrayEntry) {
                                        Log::info($debugArrayEntry);
                                    }
                                    return false;
                                }

                                // Add port hash to array of visited ports
                                array_push($visitedArray, $portHash);
                            } else {
                                // Break while loop, connection is deadwood
                                $workingPort['id'] = null;
                            }
                        } else {
                            // Break while loop, no connection found
                            $workingPort['id'] = null;
                        }
                    } else {
                        // Break while loop, no trunk found
                        $workingPort['id'] = null;
                    }
                } else {
                    // Break while loop, object is endpoint
                    $workingPort['id'] = null;
                }
            }
        }

        return true;
    }

    /**
     * Return partition 
     *
     * @param  obj  $blueprint
	 * @param  str  $face
	 * @param  arr  $partitionAddress
     * @return \Illuminate\View\View
     */
    public function getPartition($blueprint, $face, $partitionAddress)
    {
        // Locate template partition
		$partition = false;
		$partitionCollection = $blueprint[$face];
		
		foreach($partitionAddress as $partitionIndex) {
			
			if(isset($partitionCollection[$partitionIndex])) {
				
				$partition = $partitionCollection[$partitionIndex];
				$partitionCollection = $partitionCollection[$partitionIndex]['children'];
				
			} else {
				return false;
			}
		}
		
		return $partition;
    }

	/**
     * Return patched blueprint
     *
     * @param  obj  $blueprint
	 * @param  str  $face
	 * @param  arr  $partitionAddress
	 * @param  obj  $partitionPatched
     * @return \Illuminate\View\View
     */
    public function patchPartition($blueprint, $face, $partitionAddress, $partitionPatched)
    {
        // Initialize partition collection for loop
		$workingBlueprint = $blueprint;
		$partitionCollection = &$workingBlueprint[$face];
		
		// Loop over partition address until selected partition has been reached
		foreach($partitionAddress as $index => $partitionIndex) {
			
			// Test if partition address is valid
			if(isset($partitionCollection[$partitionIndex])) {
				
				// Test if selected partition has been reached
				if($index == (count($partitionAddress) - 1)) {

					// Patch partition
					$partitionCollection[$partitionIndex] = $partitionPatched;
				}
				$partitionCollection = &$partitionCollection[$partitionIndex]['children'];
				
			} else {
				return false;
			}
		}
		
		// Return entire blueprint with patched partition
		return $workingBlueprint;
    }

	/**
     * Return patched blueprint
     *
     * @param  obj  $blueprint
	 * @param  str  $face
	 * @param  arr  $partitionAddress
	 * @param  obj  $partitionPatched
     * @return \Illuminate\View\View
     */
    public function patchBlueprint(&$templateBlueprint, $inputBlueprint)
    {
        foreach($templateBlueprint as $index => &$templateBlueprintPartition) {

			$templateType = $templateBlueprintPartition['type'];

			if($templateType == 'generic') {

				$this->patchBlueprint($templateBlueprintPartition['children'], $inputBlueprint[$index]['children']);

			} else if($templateType == 'connectable') {

				$templateBlueprintPartition['port_format'] = $inputBlueprint[$index]['port_format'];
			}
		}

		return true;
    }

    /**
     * Delete object and dependencies returning entry IDs
     *
     * @param  int  $objectID
     * @param  array  $deleteArray
     * @return array
     */
    public function deleteObject($objectID, &$deleteArray)
    {
        $objectChildren = ObjectModel::where('parent_id', $objectID)->get();
        foreach($objectChildren as $objectChild) {
            $this->deleteObject($objectChild['id'], $deleteArray);
        }

        ObjectModel::where('id', $objectID)->delete();
        array_push($deleteArray['object'], $objectID);

        $trunks = TrunkModel::where('a_id', $objectID)->orWhere('b_id', $objectID)->get();
        foreach($trunks as $trunk) {
            $trunkID = $trunk['id'];
            $trunkController = new TrunkController;
            $trunkResponse = call_user_func(array($trunkController, 'destroy'), $trunkID);
            array_push($deleteArray['trunk'], $trunkResponse['id']);
        }

        $connections = ConnectionModel::where('a_id', $objectID)->orWhere('b_id', $objectID)->get();
        foreach($connections as $connection) {
            $connectionID = $connection['id'];
            $connectionController = new ConnectionController;
            $connectionResponse = call_user_func(array($connectionController, 'destroy'), $connectionID);
            array_push($deleteArray['connection'], $connectionResponse['id']);
        }

		return true;
    }

    /**
     * Delete locations and dependencies returning entry IDs
     *
     * @param  int  $locationID
     * @param  array  $deleteArray
     * @return array
     */
    public function deleteLocation($locationID, &$deleteArray)
    {
        $locationChildren = LocationModelNoImgData::where('parent_id', $locationID)->get();
        foreach($locationChildren as $locationChild) {
            $result = $this->deleteLocation($locationChild['id'], $deleteArray);
            if($result !== true) {
                return $result;
            }
        }

        // Check that objects do not exist in location
        if (ObjectModel::where('location_id', '=', $locationID)->exists()) {
            return ['id' => 'The location on one of its children contains objects and cannot be deleted.'];
        }

        // Retrieve location to delete
        $location = LocationModelNoImgData::where('id', $locationID)->first();

        // Delete image files
        $imgAttrs = array(
            'img',
        );
        foreach($imgAttrs as $imgAttr) {
            if($location[$imgAttr]) {
                $filename = 'images/'.$location[$imgAttr];
                if(Storage::disk('local')->exists($filename)) {
                    Storage::disk('local')->delete($filename);
                }
            }
        }
        $location->delete();
        array_push($deleteArray['location'], $locationID);

        // Clear stale cable paths
        $cablePaths = CablePathModel::where('cabinet_a_id', $locationID)->orWhere('cabinet_b_id', $locationID)->get();
        foreach($cablePaths as $cablePath) {
            $cablePathID = $cablePath['id'];
            $cablePathController = new CablePathController;
            $cablePathResponse = call_user_func(array($cablePathController, 'destroy'), $cablePathID);
            array_push($deleteArray['cable_path'], $cablePathResponse['id']);
        }

        // Clear stale adjacencies
        $clearAdjacencies = array(
            'left_adj_cabinet_id',
            'left_adj_cabinet_id'
        );
        foreach($clearAdjacencies as $adjacency) {
            $staleCabinets = LocationModel::where($adjacency, $locationID)->get();
            foreach($staleCabinets as $staleCabinet) {
                $staleCabinet[$adjacency] = null;
                $staleCabinet->save();
            }
        }

		return true;
    }

    /**
     * Return license data
     *
     * @param  str  $licenseKey
     * @param  str  $appID
     * @return boolean
     */
    public function fetchLicenseData($licenseKey, $appID)
    {

        $response = Http::asForm()
        ->withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ])
        ->get('https://patchcablemgr.com/api/license/'.$licenseKey.'/'.$appID);

		return $response;
    }

    /**
     * Return license portal
     *
     * @param  str  $licenseKey
     * @param  str  $appID
     * @return boolean
     */
    public function fetchLicensePortal($licenseKey, $appID)
    {

        $response = Http::asForm()
        ->withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ])
        ->get('https://patchcablemgr.com/api/license/portal/'.$licenseKey.'/'.$appID);

		return $response;
    }

    /**
     * Return catalog categories
     *
     * @return array
     */
    public function fetchCatalogCategories()
    {

        $response = Http::asForm()
        ->withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ])
        ->get('https://patchcablemgr.com/api/catalog/categories');

		return $response;
    }

    /**
     * Return catalog templates
     *
     * @return array
     */
    public function fetchCatalogTemplates()
    {

        $response = Http::asForm()
        ->withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ])
        ->get('https://patchcablemgr.com/api/catalog/templates');

		return $response;
    }

    /**
     * Return catalog templates
     *
     * @return array
     */
    public function fetchCatalogTemplate($id)
    {

        $response = Http::asForm()
        ->withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ])
        ->get('https://patchcablemgr.com/api/catalog/template/'.$id);

		return $response;
    }

    /**
     * 
     * @param  int  $length
     * Return length in centimeters
     *
     * @return array
     */
    public function converFeetToCm($length)
    {

        $cmLength = $length * 30.48;
        return round($cmLength);
        
    }

    /**
     * 
     * @param  int  $length
     * Return length in centimeters
     *
     * @return array
     */
    public function converMetersToCm($length)
    {

        $cmLength = $length * 100;
        return round($cmLength);
        
    }

    /**
     * 
     * @param  array  $validatorRules
     * @param  string $archiveAddress
     * Return transformed validation messags
     *
     * @return array
     */
    public function transformValidationMessages($validatorRules, $archiveAddress)
    {

        $msgArray = [];
        foreach($validatorRules as $validatorField => $validatorRule){
            foreach($validatorRule as $rule) {
                if(is_string($rule)) {
                    $msgID = explode(':', $rule);
                    $msgID = $msgID[0];
                    $msg = __('validation.'.$msgID);
                    $msg = (is_array($msg)) ? reset($msg) : $msg;
                    $msgArray[$validatorField.'.'.$rule] = $msg.' '.$archiveAddress;
                }
            }
        }

        // This is a hack
        foreach($msgArray as $key => $message) {
            $msgArray[explode(':', $key)[0]] = $message;
        }

        return $msgArray;
    }
}