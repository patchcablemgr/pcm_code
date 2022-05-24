<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCablePathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cable_path', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cabinet_a_id');
            $table->integer('cabinet_b_id')->default(0);
            $table->integer('distance')->default(1);
            $table->integer('path_entrance_ru');
            $table->string('notes')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cable_path');
    }
}
