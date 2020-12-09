<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gdb');
            $table->string('g1');
            $table->string('g2');
            $table->string('g3');
            $table->string('g4');
            $table->string('g5');
            $table->string('g6');
            $table->string('g7');
            $table->string('g8')->nullable();
            $table->text('loto', 500);
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->date('date');
            $table->index(['region_id', 'province_id', 'date']);
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
        Schema::dropIfExists('results');
    }
}
