<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesMediaTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('attributes_media_type', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('value');
            $table->string('name');
            $table->integer('unit_of_length');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('attributes_media_type', function (Blueprint $table) {
            
            
            
            
        });
    }
}
