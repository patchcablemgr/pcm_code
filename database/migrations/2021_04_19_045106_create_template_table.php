<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('template', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('templateName');
            $table->integer('templateCategory_id')->nullable();
            $table->string('templateType');
            $table->integer('templateRUSize')->nullable();
            $table->string('templateFunction');
            $table->boolean('templateMountConfig')->nullable();
            $table->integer('templateEncLayoutX')->nullable();
            $table->integer('templateEncLayoutY')->nullable();
            $table->integer('templateHUnits')->nullable();
            $table->integer('templateVUnits')->nullable();
            $table->integer('nestedParentEncLayoutX')->nullable();
            $table->integer('nestedParentEncLayoutY')->nullable();
            $table->integer('nestedParentHUnits')->nullable();
            $table->integer('nestedParentVUnits')->nullable();
            $table->text('templatePartitionData')->nullable();
            $table->string('frontImage', 45)->nullable();
            $table->string('rearImage', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('template', function (Blueprint $table) {
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
        });
    }
}
