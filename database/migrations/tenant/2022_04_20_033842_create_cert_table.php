<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cert', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('filename');
            $table->timestamp('valid_from');
            $table->timestamp('valid_to');
            $table->integer('key_id');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('organization');
            $table->string('cn');
            $table->string('issuer');
            $table->boolean('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cert');
    }
}
