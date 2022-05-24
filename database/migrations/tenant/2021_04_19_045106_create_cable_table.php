<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cable', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('a_id')->default(0)->index('a_id');
            $table->string('a_identifier')->default('0');
            $table->integer('a_connector')->default(0);
            $table->integer('a_object_id')->default(0);
            $table->integer('a_port_id')->default(0);
            $table->integer('a_object_face')->default(0);
            $table->integer('a_object_depth')->default(0);
            $table->integer('b_id')->default(0)->index('b_id');
            $table->string('b_identifier')->default('0');
            $table->integer('b_connector')->default(0);
            $table->integer('b_object_id')->default(0);
            $table->integer('b_port_id')->default(0);
            $table->integer('b_object_face')->default(0);
            $table->integer('b_object_depth')->default(0);
            $table->integer('media_type')->nullable()->default(0);
            $table->integer('length')->default(1);
            $table->boolean('editable')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cable');
    }
}
