<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectModel extends Model
{
    use HasFactory;
		protected $table = 'object';
    protected $casts = [
      'parent_partition_address' => 'array',
      'parent_enclosure_address' => 'array',
      'floorplan_address' => 'array',
    ];
}
