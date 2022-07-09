<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrganizationModel;
use Database\Seeders\Seeder_1_0_0;

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

    }
}
