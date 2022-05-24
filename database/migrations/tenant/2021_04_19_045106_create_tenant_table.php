<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->string('version', 15);
            $table->string('entitlement_id', 40);
            $table->integer('entitlement_last_checked');
            $table->string('entitlement_data');
            $table->string('entitlement_comment', 10000);
            $table->integer('entitlement_expiration');
            $table->boolean('global_setting_path_orientation')->default(0);
            $table->string('app_id', 40);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant');
    }
}
