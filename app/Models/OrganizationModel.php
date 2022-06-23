<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationModel extends Model
{
    use HasFactory;
    protected $table = 'organization';
    protected $casts = [
      'entitlement_data' => 'array',
    ];
    protected $hidden = ['app_id'];

    public function getVersionAttribute()
    {
        return getenv('VERSION');
    }
}
