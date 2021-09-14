<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $table = 'template';
		protected $casts = [
			'insert_constraints' => 'array',
			'blueprint' => 'array',
		];
}
