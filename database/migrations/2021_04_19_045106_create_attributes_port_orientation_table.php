<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesPortOrientationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('attributes_port_orientation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('value');
            $table->string('name');
            $table->integer('defaultOption');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('attributes_port_orientation', function (Blueprint $table) {
            
            
            
            
        });
    }
}
