<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('template_category', function (Blueprint $table) {
            $table->integer('id', true);
			$table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->string('name');
            $table->string('color');
            $table->tinyInteger('default')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('template_category', function (Blueprint $table) {
            
            
            
            
        });
    }
}
