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
        Schema::create('object', function (Blueprint $table) {
            $table->integer('id', true);
			$table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->integer('location_id');
            $table->string('name')->default('New_Object');
            $table->integer('template_id')->nullable();
            $table->integer('cabinet_ru')->nullable();
            $table->string('cabinet_front')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('parent_face')->nullable();
            $table->string('parent_partition_address')->nullable();
            $table->string('parent_enclosure_address')->nullable();
            $table->string('floorplan_address')->nullable();
            $table->string('floorplan_object_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('object');
    }
}
