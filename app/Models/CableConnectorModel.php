<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CableConnectorModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'attributes_cable_connector';
}
