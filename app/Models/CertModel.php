<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertModel extends Model
{
    use HasFactory;
    protected $table = 'cert';
    protected $casts = [
        'valid_from' => 'datetime:Y-m-d',
        'valid_to' => 'datetime:Y-m-d',
    ];
}
