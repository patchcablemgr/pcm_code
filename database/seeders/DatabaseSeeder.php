<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrganizationModel;
use Database\Seeders\Seeder_1_0_0;
use Database\Seeders\Seeder_1_0_1;
use Illuminate\Support\Facades\Log;

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

        $currentVersion = DB::table('organization')->first()->value('version');
        Log::info('attempt-1');
        Log::info($currentVersion);

        // 1.0.0 -> 1.0.1
        if(version_compare($currentVersion, '1.0.0') === 0) {
            Log::info('Upgrade to 1.0.1');
            $this->call([Seeder_1_0_1::class]);
        }

    }
}
