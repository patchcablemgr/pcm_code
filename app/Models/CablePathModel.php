<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CablePathModel extends Model
{
    protected $table = 'cable_path';
    protected $casts = [
        'a_object_partition' => 'array',
        'b_object_partition' => 'array',
      ];
}
