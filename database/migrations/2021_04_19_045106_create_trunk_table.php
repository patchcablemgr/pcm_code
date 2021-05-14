<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrunkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('trunk', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('a_id')->nullable();
            $table->integer('a_face')->nullable();
            $table->integer('a_depth')->nullable();
            $table->integer('a_port')->nullable();
            $table->boolean('a_endpoint');
            $table->integer('b_id')->nullable();
            $table->integer('b_face')->nullable();
            $table->integer('b_depth')->nullable();
            $table->integer('b_port')->nullable();
            $table->boolean('b_endpoint');
            $table->boolean('floorplan_peer')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('trunk', function (Blueprint $table) {
            
            
            
            
            
            
            
            
            
            
            
            
        });
    }
}
