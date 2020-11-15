<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_log', function (Blueprint $table) {
           $table->bigIncrements('id');
           $table->longText('message');
           $table->longText('context');
           $table->string('level')->index();
           $table->string('level_name');
           $table->string('channel')->index();
           $table->string('record_datetime');
           $table->longText('extra');
           $table->longText('formatted');
           $table->string('remote_addr')->nullable();
           $table->string('device')->nullable();
           $table->string('user_agent')->nullable();
           $table->unsignedBigInteger('user_id')->nullable();
           $table->enum('flag_solved',['Y','N'])->default('N')->nullable();
           $table->timestamps();
           $table->softDeletes();
           $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_log');
    }
}
