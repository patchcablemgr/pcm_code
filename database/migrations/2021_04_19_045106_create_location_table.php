<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('location', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 20)->default('New Node');
            $table->string('parent');
            $table->string('type');
            $table->integer('size')->default(42);
            $table->string('floorplan_img', 40)->nullable();
            $table->tinyInteger('ru_orientation')->nullable()->default(0);
            $table->integer('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('location', function (Blueprint $table) {
            
            
            
            
            
            
            
            
        });
    }
}
