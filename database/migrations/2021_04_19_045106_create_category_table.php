<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('category', function (Blueprint $table) {
            $table->integer('id', true);
			$table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->string('name');
            $table->string('color');
            $table->tinyInteger('default')->default('0');
            $table->tinyInteger('visible')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('category', function (Blueprint $table) {
            
            
            
            
        });
    }
}
