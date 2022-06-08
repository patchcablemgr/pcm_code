<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
//use Stancl\Tenancy\Database\Concerns\HasDomains;

class TenantModel extends BaseTenant implements TenantWithDatabase
{
    //use HasDatabase, HasDomains;
    use HasDatabase;

    /**
     * The attributes that sould be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'tenancy_db_username' => 'encrypted',
        'tenancy_db_password' => 'encrypted',
    ];

    /**
     * Define custom columns for this model (that shouldn't be accessed via 'data' property).
     *
     * @return array
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tenancy_db_username',
            'tenancy_db_password',
        ];
    }
}