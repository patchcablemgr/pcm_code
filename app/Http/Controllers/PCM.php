<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\CablePathController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\TrunkController;

use App\Models\CablePathModel;
use App\Models\LocationModel;
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
            for($x=$cabinetSize; $x>$newSize; $x--) {
                $ruIndex = $this->getRUIndex($x, $cabinetSize, $cabinetOrientation);
                if(in_array($ruIndex, $occupiedRUArray['front']) || in_array($ruIndex, $occupiedRUArray['rear'])) {
                    return ['cabinet_ru' => 'Cabinet cannot be smaller than highest object.'];
                }
            }

        }

        return true;
    }

	/**
     * Validate RU occupancy
     *
     * @param  int  $locationID
	 * @param  int  $templateID
	 * @param  int  $cabinetRU
     * @return arr
     */
    public function validateRUOccupancy($locationID, $templateID, $cabinetRU, $cabinetFace)
    {
        // Initialize some variables
        $collision = false;
        $occupiedRUArray = $this->generateOccupiedRUArray($locationID);

        // Validate RU occupancy
        $objectTemplate = TemplateModel::where('id', $templateID)->first();
        $objectMountConfig = $objectTemplate['mount_config'];
        $objectRUSize = $objectTemplate['ru_size'];
        for($x=0; $x<$objectRUSize; $x++) {
            $ruPosition = $cabinetRU + $x;
            if($objectMountConfig == '4-post') {
                if(in_array($ruPosition, $occupiedRUArray['front']) || in_array($ruPosition, $occupiedRUArray['rear'])) {
                    return ['cabinet_ru' => 'Destination RU is occupied.'];
                }
            } else {
                if(in_array($ruPosition, $occupiedRUArray[$cabinetFace])) {
                    return ['cabinet_ru' => 'Destination RU is occupied.'];
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
    public function validateConnectionPath($portA, $portB)
    {
        $visitedArray = array();
        $portArray = array($portA, $portB);

        foreach($portArray as $index =>$port) {

            $workingPort = $port;

            while($workingPort['id']) {

                // Generate port hash
                $portHash = md5(implode('-', array($workingPort['id'], $port['face'], json_encode($port['partition']), $port['port_id'])));
                
                // Check to see if port has been visited
                if(in_array($portHash, $visitedArray)) {
                    return false;
                }

                // Add port hash to array of visited ports
                array_push($visitedArray, $portHash);

                // Get template function
                $object = ObjectModel::where('id', $workingPort['id'])->first();
                $templateID = $object['template_id'];
                $template = TemplateModel::where('id', $templateID)->first();
                $templateFunction = $template['function'];

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
                        
                        // Check to see if port has been visited
                        if(in_array($portHash, $visitedArray)) {
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
                                
                                // Check to see if port has been visited
                                if(in_array($portHash, $visitedArray)) {
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
            $connectionResponse = call_user_func(array($connectionController, 'destroy'), $trunkID);
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
        $locationChildren = LocationModel::where('parent_id', $locationID)->get();
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

        // Delete location
        LocationModel::where('id', $locationID)->delete();
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