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
            $table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->integer('a_id');
            $table->string('a_face');
            $table->text('a_partition');
            $table->integer('a_port')->nullable();
            $table->integer('b_id');
            $table->string('b_face');
            $table->text('b_partition');
            $table->integer('b_port')->nullable();
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
