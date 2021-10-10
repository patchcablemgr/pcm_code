<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('object', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('location_id');
            $table->string('name')->default('New_Object');
            $table->integer('template_id');
            $table->integer('RU')->nullable();
            $table->integer('cabinet_front')->nullable();
            $table->integer('cabinet_back')->nullable()->default(0);
            $table->integer('parent_id')->nullable();
            $table->integer('parent_face')->nullable()->default(0);
            $table->integer('parent_depth')->nullable();
            $table->integer('insertSlotX')->nullable();
            $table->integer('insertSlotY')->nullable();
            $table->integer('position_top')->nullable();
            $table->integer('position_left')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('object', function (Blueprint $table) {
            
            
            
            
            
            
            
            
            
            
            
            
            
            
        });
    }
}
