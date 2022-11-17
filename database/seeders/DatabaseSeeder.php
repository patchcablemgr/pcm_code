<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrganizationModel;
use Database\Seeders\Seeder_1_0_0;
use Database\Seeders\Seeder_1_2_0;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Initial -> 1.0.0
        if(!OrganizationModel::all()->count()) {
            $this->call([Seeder_1_0_0::class]);
        }

        $org = OrganizationModel::where('id', 1)->first();
        $orgVersion = $org->version;

        // 1.0.0 -> 1.0.1
        if(version_compare($orgVersion, '1.0.0') === 0) {
            $org->version = '1.0.1';
            $org->save();
        }

        $org = OrganizationModel::where('id', 1)->first();
        $orgVersion = $org->version;

        // 1.0.1 -> 1.0.2
        if(version_compare($orgVersion, '1.0.1') === 0) {
            $org->version = '1.0.2';
            $org->save();
        }

        $orgVersion = $org->version;

        // 1.0.2 -> 1.1.0
        if(version_compare($orgVersion, '1.0.2') === 0) {
            $org->version = '1.1.0';
            $org->save();
        }

        $orgVersion = $org->version;

        // 1.1.0 -> 1.2.0
        if(version_compare($orgVersion, '1.1.0') === 0) {
            $this->call([Seeder_1_2_0::class]);
            $org->version = '1.2.0';
            $org->save();
        }

        $orgVersion = $org->version;

        // 1.2.0 -> 1.2.1
        if(version_compare($orgVersion, '1.2.0') === 0) {
            $org->version = '1.2.1';
            $org->save();
        }

        $orgVersion = $org->version;

        // 1.2.1 -> 1.2.2
        if(version_compare($orgVersion, '1.2.1') === 0) {
            $org->version = '1.2.2';
            $org->save();
        }

    }
}
