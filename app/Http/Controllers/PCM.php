<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
     * Validate RU occupancy
     *
     * @param  int  $locationID
	 * @param  int  $templateID
	 * @param  int  $cabinetRU
     * @return arr
     */
    public function validateRUOccupancy($locationID, $templateID, $cabinetRU, $cabinetFace)
    {
        // Initialize some variables for validating RU occupancy
        $occupiedRUs = array('front' => [], 'rear' => []);
        $collision = false;

        // Populate occupied RU array
        $cabinetObjects = ObjectModel::where('location_id', $locationID)->get();
        foreach($cabinetObjects as $cabinetObject) {
            $cabinetObjectTemplateID = $cabinetObject['template_id'];
            $cabinetObjectCabinetFront = $cabinetObject['cabinet_front'];
            $cabinetObjectCabinetRU = $cabinetObject['cabinet_ru'];
            $cabinetObjectTemplate = TemplateModel::where('id', $cabinetObjectTemplateID)->first();
            $cabinetObjectMountConfig = $cabinetObjectTemplate['mount_config'];
            $cabinetObjectRUSize = $cabinetObjectTemplate['ru_size'];
            for($x=1; $x<=$cabinetObjectRUSize; $x++) {
                $ruPosition = $cabinetObjectCabinetRU + $x;
                if($cabinetObjectMountConfig == '4-post') {
                    array_push($occupiedRUs['front'], $ruPosition);
                    array_push($occupiedRUs['rear'], $ruPosition);
                } else {
                    array_push($occupiedRUs[$cabinetObjectCabinetFront], $ruPosition);
                }
            }
        }

        // Validate RU occupancy
        $objectTemplate = TemplateModel::where('id', $templateID)->first();
        $objectMountConfig = $objectTemplate['mount_config'];
        $objectRUSize = $objectTemplate['ru_size'];
        for($x=1; $x<=$objectRUSize; $x++) {
            $ruPosition = $cabinetRU + $x;
            if($objectMountConfig == '4-post') {
                if(in_array($ruPosition, $occupiedRUs['front']) || in_array($ruPosition, $occupiedRUs['rear'])) {
                    return ['cabinet_ru' => 'Destination RU is occupied.'];
                }
            } else {
                if(in_array($ruPosition, $occupiedRUs[$cabinetFace])) {
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
    public function validateEnclosureOccupancy($objectParentID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress)
    {
        // Validate parent object enclosure occupancy
        $parentsFound = ObjectModel::where(['parent_id' => $objectParentID, 'parent_face' => $objectParentFace])->get();
        foreach($parentsFound as $parent) {
            if($parent['parent_partition_address'] == $objectParentPartitionAddress && $parent['parent_enclosure_address'] == $objectParentEnclosureAddress) {
                return ['parentEnclosureAddress' => 'Destination enclosure slot is occupied.'];
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
     * Return patched blueprint
     *
     * @param  int  $objectID
     * @return boolean
     */
    public function deleteObject($objectID)
    {
        $objectChildren = ObjectModel::where('parent_id', $objectID)->get();
        foreach($objectChildren as $objectChild) {
            $this->deleteObject($objectChild['id']);
        }

        ObjectModel::where('id', $objectID)->delete();
        TrunkModel::where('a_id', $objectID)->orWhere('b_id', $objectID)->delete();
        ConnectionModel::where('a_id', $objectID)->orWhere('b_id', $objectID)->delete();

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
}