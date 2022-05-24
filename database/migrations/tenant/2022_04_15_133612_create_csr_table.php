<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csr', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('csr');
            $table->integer('user_id');
            $table->integer('key_id');
            $table->integer('cert_id')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('organization');
            $table->string('cn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csr');
    }
}
