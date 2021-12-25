<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TemplateModel extends Model
{
    protected $table = 'template';
	protected $casts = [
		'insert_constraints' => 'array',
		'blueprint' => 'array',
	];

	/**
    * Mutate template image
    *
    * @param  string  $value
    * @return string
    */
    public function getImageFrontAttribute($value)
    {
		if($value) {
			$imgPath = '/images/'.$value;
            $imgFile = Storage::get($imgPath);
            $imgMimeType = Storage::mimeType($imgPath);
            $imgBase64 = base64_encode($imgFile);
            return 'data:'.$imgMimeType.';base64,'.$imgBase64;
		} else {

            return $value;
        }
    }

	/**
    * Mutate template image
    *
    * @param  string  $value
    * @return string
    */
    public function getImageRearAttribute($value)
    {
        if($value) {
			$imgPath = '/images/'.$value;
            $imgFile = Storage::get($imgPath);
            $imgMimeType = Storage::mimeType($imgPath);
            $imgBase64 = base64_encode($imgFile);
            return 'data:'.$imgMimeType.';base64,'.$imgBase64;
		} else {

            return $value;
        }
    }
}
