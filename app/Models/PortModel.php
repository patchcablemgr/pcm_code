<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortModel extends Model
{
    use HasFactory;
    protected $table = 'port';

    protected $casts = [
		'object_partition' => 'array',
	];
}
