<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterTableCablePath extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cable_path', function (Blueprint $table) {
            $table->timestamps();
            DB::statement('ALTER TABLE cable_path ALTER COLUMN cabinet_b_id DROP DEFAULT');
            DB::statement('ALTER TABLE cable_path ALTER COLUMN distance DROP DEFAULT');
            $table->dropColumn('path_entrance_ru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
