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
        Schema::create('location', function (Blueprint $table) {
            $table->integer('id', true);
			$table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->string('name', 255)->default('New Node');
            $table->integer('parent_id')->nullable();
            $table->string('type');
            $table->integer('size')->nullable();
            $table->string('img')->nullable();
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
        Schema::dropIfExists('location');
    }
}
