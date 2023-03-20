<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\TemplateModel;
use App\Models\LocationModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class RUOccupancy implements Rule
{
    /**
     * @param  int  $locationID
	 * @param  int  $templateID
	 * @param  int  $cabinetRU
     * @param  int  $cabinetFront
     * @return bool
     */
    public function __construct($objectID, $locationID, $templateID, $cabinetRU, $cabinetFront, $archiveAddress)
    {

        $this->returnMessage = 'Unknown error.';

        // Initialize some variables
        $this->object_id = $objectID;
        $this->location_id = $locationID;
        $this->template_id = $templateID;
        $this->cabinet_ru = $cabinetRU;
        $this->cabinet_front = $cabinetFront;
        $this->archiveAddress = $archiveAddress;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $PCM = new PCM;
        $objectID = $this->object_id;
        $locationID = $this->location_id;
        $templateID = $this->template_id;
        $cabinetRU = $this->cabinet_ru;
        $cabinetFace = $this->cabinet_front;
        $archiveAddress = $this->archiveAddress;

        // Initialize some variables
        $collision = false;
        $occupiedRUArray = $PCM->generateOccupiedRUArray($locationID);

        // Convert cabinet RU
        $location = LocationModel::where('id', $locationID)->first();
        $cabinetSize = $location->size;
        $cabinetOrientation = $location->ru_orientation;
        $cabinetRU = $PCM->getRUIndex($cabinetRU, $cabinetSize, $cabinetOrientation);

        // Validate RU occupancy
        $objectTemplate = TemplateModel::where('id', $templateID)->first();
        $objectMountConfig = $objectTemplate['mount_config'];
        $objectRUSize = $objectTemplate['ru_size'];
        for($x=0; $x<$objectRUSize; $x++) {
            $ruPosition = $cabinetRU + $x;
            if($objectMountConfig == '4-post') {
                if(isset($occupiedRUArray['front'][$ruPosition])) {
                    if($occupiedRUArray['front'][$ruPosition] != $objectID) {
                        $collision = true;
                    }
                }
                if(isset($occupiedRUArray['rear'][$ruPosition])) {
                    if($occupiedRUArray['rear'][$ruPosition] != $objectID) {
                        $collision = true;
                    }
                }
            } else {
                if(isset($occupiedRUArray[$cabinetFace][$ruPosition])) {
                    if($occupiedRUArray[$cabinetFace][$ruPosition] != $objectID) {
                        $collision = true;
                    }
                }
            }
        }

        if($collision) {
            $this->returnMessage = 'Destination RU is occupied.'.$this->archiveAddress;
            return false;
        } else {
            return true;
        }
    }

    /**
    * Get the validation error message.
    *
    * @return string
    */
    public function message()
    {
        return $this->returnMessage;
    }
}
