<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerusahaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_ps',100)->nullable();
            $table->text('alamat_ps')->nullable();
            $table->string('email_ps',100)->nullable();
            $table->string('fax_ps',100)->nullable();
            $table->string('telp_ps',100)->nullable();
            $table->string('website_ps',100)->nullable();
            $table->date('tgl_berdiri_ps')->nullable();
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
        Schema::dropIfExists('perusahaan');
    }
}
