<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrunkModel extends Model
{
    use HasFactory;
    protected $table = 'trunk';
    protected $casts = [
		'a_partition' => 'array',
		'b_partition' => 'array',
	];
}
