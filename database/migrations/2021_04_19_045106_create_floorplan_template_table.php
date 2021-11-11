<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFloorplanTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('floorplan_template', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('type');
            $table->string('name');
            $table->string('icon');
            $table->string('function');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('floorplan_template', function (Blueprint $table) {
            
            
            
            
        });
    }
}
