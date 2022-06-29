<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamps();
            $table->string('name');
            $table->string('license_key', 40)->nullable();
            $table->integer('license_last_checked')->nullable();
            $table->string('license_data')->nullable();
            $table->string('app_id', 40);
            $table->string('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization');
    }
}
