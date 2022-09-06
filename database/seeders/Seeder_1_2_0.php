<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LocationModel;

class Seeder_1_2_0 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $locations = LocationModel::all();

        foreach($locations as $location) {
            if($location->type == 'cabinet') {
                $location->ru_orientation = 'bottom-up';
            } else {
                $location->ru_orientation = null;
            }
            $location->save();
        }

    }
}
