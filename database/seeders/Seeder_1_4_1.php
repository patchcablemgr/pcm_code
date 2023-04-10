<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PortConnectorModel;
use App\Models\CableConnectorModel;

class Seeder_1_4_1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Create "ST" port connector
        PortConnectorModel::firstOrCreate(
            ['value' => 8],
            ['value' => 8, 'name' => 'ST', 'type_id' => 2, 'default' => 0]
        );

        // Create "ST" cable connector
        CableConnectorModel::firstOrCreate(
            ['value' => 8],
            ['value' => 8, 'name' => 'ST', 'type_id' => 2, 'default' => 0]
        );

    }
}
