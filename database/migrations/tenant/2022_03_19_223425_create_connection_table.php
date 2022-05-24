<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('created_at');
			$table->dateTime('updated_at');
            $table->integer('a_id');
            $table->string('a_face');
            $table->text('a_partition');
            $table->integer('a_port');
            $table->integer('b_id')->nullable();
            $table->string('b_face')->nullable();
            $table->text('b_partition')->nullable();
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
        Schema::dropIfExists('connection');
    }
}
