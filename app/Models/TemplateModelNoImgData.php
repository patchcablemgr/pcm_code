<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TemplateModelNoImgData extends Model
{
    protected $table = 'template';
	protected $casts = [
		'insert_constraints' => 'array',
		'blueprint' => 'array',
	];

}
