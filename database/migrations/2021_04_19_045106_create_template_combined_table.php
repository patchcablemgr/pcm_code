<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateCombinedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('template_combined', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('templateName');
            $table->integer('template_id')->nullable();
            $table->integer('templateCategory_id')->nullable();
            $table->text('childTemplateData')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('template_combined', function (Blueprint $table) {
            
            
            
            
            
        });
    }
}
