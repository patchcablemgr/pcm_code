<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePort extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('port', function (Blueprint $table) {
            
            if(!$this->columnExists($table, 'created_at') and !$this->columnExists($table, 'updated_at')) {
                $table->timestamps();
            }

            if(!$this->columnExists($table, 'description')) {
                $table->string('description', 255);
            }

            if($this->columnExists($table, 'object_depth')) {
                $table->renameColumn('object_depth', 'object_partition')->change();
            }
            $table->string('object_face', 255)->change();
            
        });

        Schema::table('port', function (Blueprint $table) {

            $table->string('object_partition', 255)->change();
            
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
