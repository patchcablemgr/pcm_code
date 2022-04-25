<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSRModel extends Model
{
    use HasFactory;
    protected $table = 'csr';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i e',
    ];
}
