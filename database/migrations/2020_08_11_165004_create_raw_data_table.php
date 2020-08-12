<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tgl_transaksi')->nullable();
            $table->string('no_nota',20)->nullable();
            $table->float('pasir')->nullable();
            $table->float('gendol')->nullable();
            $table->float('abu')->nullable();
            $table->float('split2_3')->nullable();
            $table->float('split1_2')->nullable();
            $table->float('lpa')->nullable();
            $table->string('campur',1)->nullable();
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
        Schema::dropIfExists('raw_data');
    }
}
