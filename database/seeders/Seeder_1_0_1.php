<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrganizationModel;

class Seeder_1_0_1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Version
        $version = '1.0.1';

        // Update app version
        $org = OrganizationModel::where('id', 1)->first();
        $org->version = $version;
        $org->save();

    }
}
