<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template', function (Blueprint $table) {

            if($this->columnExists($table, 'image_front')) {
                $table->renameColumn('image_front', 'img_front')->change();
            }

            if($this->columnExists($table, 'image_rear')) {
                $table->renameColumn('image_rear', 'img_rear')->change();
            }
            
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
