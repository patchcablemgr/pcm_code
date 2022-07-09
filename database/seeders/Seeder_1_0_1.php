<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        // Insert test field
        $testFieldArray = [
            [ 'field' => 'test1']
        ];

        foreach($testFieldArray as $testField) {
            DB::table('test')->insert($testField);
        }

        // Update app version
        DB::table('organization')
            ->first()
            ->update(['version' => $version]);
    }
}
