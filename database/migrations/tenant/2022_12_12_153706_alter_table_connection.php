<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableConnection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('connection', function (Blueprint $table) {
            
            $table->string('a_cable_id')->nullable()->default(null);
            $table->string('b_cable_id')->nullable()->default(null);
            
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

    /**
     * Determine if column exists
     *
     * @return boolean
     */
    public function columnExists($tbl,$column)
    {
        if (Schema::hasColumn($tbl->getTable(), $column)) {
            return true;
        } else {
            return false;
        }
    }
}
