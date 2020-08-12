<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('config_name',255)->nullable();
            $table->string('table_source',255)->nullable();
            $table->string('config_value',255)->nullable();
            $table->string('description',255)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_ids');
    }
}
