<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationModel extends Model
{
    use HasFactory;
    protected $table = 'organization';
    protected $casts = [
      'license_data' => 'array',
    ];
    protected $hidden = ['app_id'];

}
