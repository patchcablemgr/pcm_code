<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    use HasFactory;
    protected $table = 'template';
		protected $casts = [
			'insert_constraints' => 'array',
			'blueprint' => 'array',
		];
}
