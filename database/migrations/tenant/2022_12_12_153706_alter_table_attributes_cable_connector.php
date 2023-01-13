<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAttributesCableConnector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attributes_cable_connector', function (Blueprint $table) {
            
            if(!$this->columnExists($table, 'created_at') and !$this->columnExists($table, 'updated_at')) {
                $table->timestamps();
            }

            if(!$this->columnExists($table, 'type_id')) {
                $table->integer('type_id');
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
