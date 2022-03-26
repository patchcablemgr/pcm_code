<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionModel extends Model
{
    use HasFactory;
    protected $table = 'connection';
    protected $casts = [
        'a_partition' => 'array',
        'b_partition' => 'array',
    ];
}
