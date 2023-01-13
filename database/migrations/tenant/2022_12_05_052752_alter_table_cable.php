<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('cable', function (Blueprint $table) {

            if($this->columnExists($table, 'a_id')) {
                $table->dropColumn('a_id');
            }
            if($this->columnExists($table, 'b_id')) {
                $table->dropColumn('b_id');
            }

            $table->dropColumn('a_object_id');
            $table->dropColumn('a_object_face');
            $table->dropColumn('a_object_depth');
            $table->dropColumn('a_port_id');
            $table->dropColumn('b_object_id');
            $table->dropColumn('b_object_face');
            $table->dropColumn('b_object_depth');
            $table->dropColumn('b_port_id');

            $table->timestamps();
            $table->renameColumn('a_identifier', 'a_id')->change();
            $table->renameColumn('b_identifier', 'b_id')->change();
            $table->renameColumn('a_connector', 'a_connector_id')->change();
            $table->renameColumn('b_connector', 'b_connector_id')->change();
            $table->renameColumn('media_type', 'media_id')->change();
            $table->integer('length')->nullable()->default(null)->change();
        });

        Schema::table('cable', function (Blueprint $table) {
            $table->string('a_id')->nullable()->default(null)->change();
            $table->integer('a_connector_id')->nullable()->default(null)->change();
            $table->string('b_id')->nullable()->default(null)->change();
            $table->integer('b_connector_id')->nullable()->default(null)->change();
            $table->integer('media_id')->nullable()->default(null)->change();
        });
        
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
