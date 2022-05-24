<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabinetAdjacencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabinet_adjacency', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('left_cabinet_id')->nullable();
            $table->integer('right_cabinet_id')->nullable();
            $table->integer('entrance_ru')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cabinet_adjacency');
    }
}
