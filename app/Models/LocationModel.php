<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LocationModel extends Model
{
    protected $table = 'location';

    /**
    * Get the user's first name.
    *
    * @param  string  $value
    * @return string
    */
    public function getImgAttribute($value)
    {
        if($this->type == 'floorplan') {

            $imgPath = ($value) ? '/images/'.$value : '/images/floorplan-default.png';
            $imgFile = Storage::get($imgPath);
            $imgMimeType = Storage::mimeType($imgPath);
            $imgBase64 = base64_encode($imgFile);
            return 'data:'.$imgMimeType.';base64,'.$imgBase64;
        } else {

            return $value;
        }
    }
}
