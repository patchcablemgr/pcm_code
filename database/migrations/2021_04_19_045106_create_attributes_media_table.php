<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('attributes_media', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('value');
            $table->string('name');
            $table->integer('media_category_id');
            $table->integer('media_type_id');
            $table->tinyInteger('default');
            $table->tinyInteger('display');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('attributes_media', function (Blueprint $table) {
            
            
            
            
            
            
            
        });
    }
}
