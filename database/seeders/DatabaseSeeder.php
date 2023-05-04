<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrganizationModel;
use Database\Seeders\Seeder_1_0_0;
use Database\Seeders\Seeder_1_2_0;
use Database\Seeders\Seeder_1_3_0;
use Database\Seeders\Seeder_1_4_1;

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

        // 1.0.0 -> 1.0.1
        if(version_compare($org->version, '1.0.0') === 0) {
            $org->version = '1.0.1';
            $org->save();
        }

        // 1.0.1 -> 1.0.2
        if(version_compare($org->version, '1.0.1') === 0) {
            $org->version = '1.0.2';
            $org->save();
        }

        // 1.0.2 -> 1.1.0
        if(version_compare($org->version, '1.0.2') === 0) {
            $org->version = '1.1.0';
            $org->save();
        }

        // 1.1.0 -> 1.2.0
        if(version_compare($org->version, '1.1.0') === 0) {
            $this->call([Seeder_1_2_0::class]);
            $org->version = '1.2.0';
            $org->save();
        }

        // 1.2.0 -> 1.2.1
        if(version_compare($org->version, '1.2.0') === 0) {
            $org->version = '1.2.1';
            $org->save();
        }

        // 1.2.1 -> 1.2.2
        if(version_compare($org->version, '1.2.1') === 0) {
            $org->version = '1.2.2';
            $org->save();
        }

        // 1.2.2 -> 1.2.3
        if(version_compare($org->version, '1.2.2') === 0) {
            $org->version = '1.2.3';
            $org->save();
        }

        // 1.2.3 -> 1.2.4
        if(version_compare($org->version, '1.2.3') === 0) {
            $org->version = '1.2.4';
            $org->save();
        }

        // 1.2.4 -> 1.3.0
        if(version_compare($org->version, '1.2.4') === 0) {
            $this->call([Seeder_1_3_0::class]);
            $org->version = '1.3.0';
            $org->save();
        }

        // 1.3.0 -> 1.3.1
        if(version_compare($org->version, '1.3.0') === 0) {
            $org->version = '1.3.1';
            $org->save();
        }

        // 1.3.1 -> 1.4.0
        if(version_compare($org->version, '1.3.1') === 0) {
            $org->version = '1.4.0';
            $org->save();
        }

        // 1.4.0 -> 1.4.1
        if(version_compare($org->version, '1.4.0') === 0) {
            // Rerun this seeder as it was not included in v1.3.0
            $this->call([Seeder_1_3_0::class]);
            $this->call([Seeder_1_4_1::class]);
            $org->version = '1.4.1';
            $org->save();
        }

        // 1.4.1 -> 1.4.2
        if(version_compare($org->version, '1.4.1') === 0) {
            $org->version = '1.4.2';
            $org->save();
        }

        // 1.4.2 -> 1.4.3
        if(version_compare($org->version, '1.4.2') === 0) {
            $org->version = '1.4.3';
            $org->save();
        }

        // 1.4.3 -> 1.4.4
        if(version_compare($org->version, '1.4.3') === 0) {
            $org->version = '1.4.4';
            $org->save();
        }

        // 1.4.4 -> 1.4.5
        if(version_compare($org->version, '1.4.4') === 0) {
            $org->version = '1.4.5';
            $org->save();
        }

        // 1.4.5 -> 1.4.6
        if(version_compare($org->version, '1.4.5') === 0) {
            $org->version = '1.4.6';
            $org->save();
        }

        // 1.4.6 -> 1.4.7
        if(version_compare($org->version, '1.4.6') === 0) {
            $org->version = '1.4.7';
            $org->save();
        }

        // 1.4.7 -> 1.4.8
        if(version_compare($org->version, '1.4.7') === 0) {
            $org->version = '1.4.8';
            $org->save();
        }

        // 1.4.8 -> 1.4.9
        if(version_compare($org->version, '1.4.8') === 0) {
            $org->version = '1.4.9';
            $org->save();
        }

    }
}
