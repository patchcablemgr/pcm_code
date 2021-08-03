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
            $table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->string('name');
            $table->integer('category_id')->nullable();
            $table->string('type');
            $table->integer('ru_size')->nullable();
            $table->string('function');
            $table->string('mount_config')->nullable();
            $table->integer('enclosure_layout_x')->nullable();
            $table->integer('enclosure_layout_y')->nullable();
            $table->integer('partition_layout_x')->nullable();
            $table->integer('partition_layout_y')->nullable();
            $table->integer('nested_enclosure_layout_x')->nullable();
            $table->integer('nested_enclosure_layout_y')->nullable();
            $table->integer('nested_partition_layout_x')->nullable();
            $table->integer('nested_partition_layout_y')->nullable();
            $table->text('blueprint')->nullable();
            $table->string('image_front', 45)->nullable();
            $table->string('image_rear', 45)->nullable();
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
