<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // attributes_port_connector
        $portConnectorArray = [
            [ 'value' => 1, 'name' => 'RJ45', 'category_type_id' => 1, 'default' => 1],
            [ 'value' => 2, 'name' => 'LC', 'category_type_id' => 2, 'default' => 0],
            [ 'value' => 3, 'name' => 'SC', 'category_type_id' => 2, 'default' => 0],
            [ 'value' => 4, 'name' => 'SFP', 'category_type_id' => 4, 'default' => 0],
            [ 'value' => 5, 'name' => 'QSFP', 'category_type_id' => 4, 'default' => 0],
            [ 'value' => 6, 'name' => 'MPO-12', 'category_type_id' => 2, 'default' => 0],
            [ 'value' => 7, 'name' => 'MPO-24', 'category_type_id' => 2, 'default' => 0]
        ];

        foreach($portConnectorArray as $portConnector) {
            DB::table('attributes_port_connector')->insert($portConnector);
        }

        // attributes_port_orientation
        $portOrientationArray = [
            [ 'value' => 1, 'name' => 'Top-Left to Right', 'default' => 1],
            [ 'value' => 2, 'name' => 'Top-Left to Bottom', 'default' => 0],
            [ 'value' => 3, 'name' => 'Top-Right to Left', 'default' => 0],
            [ 'value' => 4, 'name' => 'Bottom-Left to Right', 'default' => 0],
            [ 'value' => 5, 'name' => 'Bottom-Left to Top', 'default' => 0]
        ];

        foreach($portOrientationArray as $portOrientation) {
            DB::table('attributes_port_orientation')->insert($portOrientation);
        }

        // attributes_media
        $mediaArray = [
            [ 'value' => 1, 'name' => 'Cat5e', 'category_id' => 1, 'type_id' => 1, 'display' => 1, 'default' => 1],
            [ 'value' => 2, 'name' => 'Cat6', 'category_id' => 1, 'type_id' => 1, 'display' => 1, 'default' => 0],
            [ 'value' => 3, 'name' => 'Cat6a', 'category_id' => 1, 'type_id' => 1, 'display' => 1, 'default' => 0],
            [ 'value' => 5, 'name' => 'SM-OS1', 'category_id' => 4, 'type_id' => 2, 'display' => 1, 'default' => 0],
            [ 'value' => 6, 'name' => 'MM-OM4', 'category_id' => 2, 'type_id' => 2, 'display' => 1, 'default' => 0],
            [ 'value' => 7, 'name' => 'MM-OM3', 'category_id' => 2, 'type_id' => 2, 'display' => 1, 'default' => 0],
            [ 'value' => 8, 'name' => 'Unspecified', 'category_id' => 5, 'type_id' => 4, 'display' => 0, 'default' => 0]
        ];

        foreach($mediaArray as $media) {
            DB::table('attributes_media')->insert($media);
        }

        // attributes_media_type
        $mediaTypeArray = [
            [ 'value' => 1, 'name' => 'Copper', 'unit_of_length' => 'ft.'],
            [ 'value' => 2, 'name' => 'Fiber', 'unit_of_length' => 'm.'],
            [ 'value' => 3, 'name' => 'Label', 'unit_of_length' => ''],
            [ 'value' => 4, 'name' => 'Unspecified', 'unit_of_length' => 'm.']
        ];

        foreach($mediaTypeArray as $mediaType) {
            DB::table('attributes_media_type')->insert($mediaType);
        }

        // attributes_media_category
        $mediaCategoryArray = [
            [ 'value' => 1, 'name' => 'Copper', 'category_type_id' => 1],
            [ 'value' => 2, 'name' => 'MultimodeFiber', 'category_type_id' => 2],
            [ 'value' => 3, 'name' => 'Label', 'category_type_id' => 3],
            [ 'value' => 4, 'name' => 'Singlemode Fiber', 'category_type_id' => 2],
            [ 'value' => 5, 'name' => 'Unspecified', 'category_type_id' => 4]
        ];

        foreach($mediaCategoryArray as $mediaCategory) {
            DB::table('attributes_media_category')->insert($mediaCategory);
        }

        // attributes_cable_connector
        $cableConnectorArray = [
            [ 'value' => 1, 'name' => 'RJ45', 'default' => 1],
            [ 'value' => 2, 'name' => 'LC', 'default' => 0],
            [ 'value' => 3, 'name' => 'SC', 'default' => 0],
            [ 'value' => 4, 'name' => 'Label', 'default' => 0],
            [ 'value' => 5, 'name' => 'MPO-12', 'default' => 0],
            [ 'value' => 6, 'name' => 'MPO-24', 'default' => 0]
        ];

        foreach($cableConnectorArray as $cableConnector) {
            DB::table('attributes_cable_connector')->insert($cableConnector);
        }

        // location
        $LocationArray = [
            [ 'created_at' => now(), 'updated_at' => now(), 'name' => 'Location', 'parent_id' => 0, 'type' => 'location']
        ];

        foreach($LocationArray as $location) {
            DB::table('location')->insert($location);
        }

        // category
        $categoryArray = [
            [ 'id' => 1, 'created_at' => now(), 'updated_at' => now(), 'name' => 'Patch_Panel', 'color' => '#9B9B9BFF', 'default' => 1],
            [ 'id' => 2, 'created_at' => now(), 'updated_at' => now(), 'name' => 'Switch', 'color' => '#B8E986FF', 'default' => 0],
            [ 'id' => 3, 'created_at' => now(), 'updated_at' => now(), 'name' => 'Router', 'color' => '#4A90E2FF', 'default' => 0],
            [ 'id' => 4, 'created_at' => now(), 'updated_at' => now(), 'name' => 'Server', 'color' => '#50E3C2FF', 'default' => 0]
        ];

        foreach($categoryArray as $category) {
            DB::table('category')->insert($category);
        }

        // template
        $templateArray = [
            [ 'id' => 1, 'created_at' => now(), 'updated_at' => now(), 'name' => 'Switch', 'category_id' => 2, 'type' => 'standard', 'ru_size' => 1, 'function' => 'endpoint', 'mount_config' => '4-post', 'blueprint' => '{"front":[{"type":"connectable","units":20,"children":[],"port_format":[{"type":"static","value":"G1\/0\/","count":0,"order":0},{"type":"incremental","value":"1","count":0,"order":1}],"port_layout":{"cols":"24","rows":"2"},"media":1,"port_connector":1,"port_orientation":2},{"type":"enclosure","units":4,"children":[],"enc_layout":{"cols":1,"rows":1}}],"rear":[{"type":"generic","units":1,"children":[{"type":"connectable","units":1,"children":[],"port_format":[{"type":"static","value":"Con","count":0,"order":0}],"port_layout":{"cols":1,"rows":1},"media":1,"port_connector":1,"port_orientation":1},{"type":"connectable","units":1,"children":[],"port_format":[{"type":"static","value":"Mgmt","count":0,"order":0}],"port_layout":{"cols":1,"rows":1},"media":1,"port_connector":1,"port_orientation":1}]}]}'],
        ];

        foreach($templateArray as $template) {
            DB::table('template')->insert($template);
        }

        // attributes_history_function
        $historyFunctionArray = [
            [ 'value' => 1, 'name' => 'Build->Templates'],
            [ 'value' => 2, 'name' => 'Build->Cabinets'],
            [ 'value' => 3, 'name' => 'Explore'],
            [ 'value' => 4, 'name' => 'Scan'],
            [ 'value' => 5, 'name' => 'Inventory'],
            [ 'value' => 6, 'name' => 'Admin->General'],
            [ 'value' => 7, 'name' => 'Admin->Integration']
        ];

        foreach($historyFunctionArray as $historyFunction) {
            DB::table('attributes_history_function')->insert($historyFunction);
        }

        // attributes_history_action
        $historyActionArray = [
            [ 'value' => 1, 'name' => 'Create'],
            [ 'value' => 2, 'name' => 'Update'],
            [ 'value' => 3, 'name' => 'Delete']
        ];

        foreach($historyActionArray as $historyAction) {
            DB::table('attributes_history_action')->insert($historyAction);
        }
    }
}
