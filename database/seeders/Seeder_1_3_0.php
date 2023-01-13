<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CableConnectorModel;
use App\Models\MediaCategoryModel;
use App\Models\MediaTypeModel;
use App\Models\TrunkModel;
use App\Models\ObjectModel;

class Seeder_1_3_0 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete "Label" cable connector
        CableConnectorModel::where('name', 'Label')->delete();

        // Delete "Label" media category
        MediaCategoryModel::where('name', 'Label')->delete();

        // Delete "Label" media type
        MediaTypeModel::where('name', 'Label')->delete();

        // Create "Unspecified" cable connector
        CableConnectorModel::firstOrCreate(
            ['value' => 7],
            ['value' => 7, 'name' => 'Unspecified', 'default' => 0, 'type_id' => 4]
        );

        // Nullify non-floorplan object trunk ports
        foreach (TrunkModel::all() as $trunk) {
            $aObject = ObjectModel::where('id', $trunk->a_id)->first();
            if($aObject->floorplan_object_type == null) {
                $trunk->a_port = null;
                $trunk->b_port = null;
                $trunk->save();
            }
        }

        // Update cable connectors with media type id
        CableConnectorModel::where('value', 1)->update(array('type_id' => 1));
        CableConnectorModel::where('value', 2)->update(array('type_id' => 2));
        CableConnectorModel::where('value', 3)->update(array('type_id' => 2));
        CableConnectorModel::where('value', 5)->update(array('type_id' => 2));
        CableConnectorModel::where('value', 6)->update(array('type_id' => 2));

    }
}
