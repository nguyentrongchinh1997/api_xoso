<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultVietlottsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_vietlotts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vietlott_id');
            $table->string('g1');
            $table->string('g2');
            $table->string('g3');
            $table->string('gkk1');
            $table->string('gkk2');
            $table->string('amount_3d')->nullable();
            $table->string('amount_3d_plus')->nullable();
            $table->date('date');
            $table->index(['date', 'vietlott_id']);
            $table->foreign('vietlott_id')->references('id')->on('vietlotts');
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
        Schema::dropIfExists('result_vietlotts');
    }
}
