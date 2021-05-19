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
		//
		// Media Type
		//
        DB::table('attributes_media_type')->insert([
			[
				'value' => 1,
				'name' => 'Copper',
				'unit_of_length' => 0
			],
			[
				'value' => 2,
				'name' => 'Fiber',
				'unit_of_length' => 1
			],
			[
				'value' => 3,
				'name' => 'Unspecified',
				'unit_of_length' => 1
			],
		]);
		
		//
		// Media Category
		//
		DB::table('attributes_media_category')->insert([
			[
				'value' => 1,
				'name' => 'Copper',
				'media_type_id' => 1
			],
			[
				'value' => 2,
				'name' => 'Multimode Fiber',
				'media_type_id' => 2
			],
			[
				'value' => 3,
				'name' => 'Singlemode Fiber',
				'media_type_id' => 2
			],
			[
				'value' => 4,
				'name' => 'Unspecified',
				'media_type_id' => 3
			],
		]);
		
		//
		// Media
		//
		DB::table('attributes_media')->insert([
			[
				'value' => 1,
				'name' => 'Cat5e',
				'media_category_id' => 1,
				'media_type_id' => 1,
				'default' => 1,
				'display' => 1
			],
			[
				'value' => 2,
				'name' => 'Cat6',
				'media_category_id' => 1,
				'media_type_id' => 1,
				'default' => 0,
				'display' => 1
			],
			[
				'value' => 3,
				'name' => 'Cat6a',
				'media_category_id' => 1,
				'media_type_id' => 1,
				'default' => 0,
				'display' => 1
			],
			[
				'value' => 4,
				'name' => 'SM-OS1',
				'media_category_id' => 3,
				'media_type_id' => 2,
				'default' => 0,
				'display' => 1
			],
			[
				'value' => 5,
				'name' => 'MM-OM4',
				'media_category_id' => 2,
				'media_type_id' => 2,
				'default' => 0,
				'display' => 1
			],
			[
				'value' => 6,
				'name' => 'MM-OM3',
				'media_category_id' => 2,
				'media_type_id' => 2,
				'default' => 0,
				'display' => 1
			],
			[
				'value' => 7,
				'name' => 'Unspecified',
				'media_category_id' => 4,
				'media_type_id' => 3,
				'default' => 0,
				'display' => 0
			],
		]);
    }
}
