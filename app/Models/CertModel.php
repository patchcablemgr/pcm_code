<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertModel extends Model
{
    use HasFactory;
    protected $table = 'cert';
    protected $casts = [
        'valid_from' => 'datetime:Y-m-d H:i e',
        'valid_to' => 'datetime:Y-m-d H:i e',
        'active' => 'boolean',
    ];
}
