<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXsdtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xsdts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dt123')->comment('123')->nullable();
            $table->string('dt6x36')->comment('6x36')->nullable();
            $table->string('dt4')->comment('thần tài 4')->nullable();
            $table->date('date');
            $table->index(['date']);
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
        Schema::dropIfExists('xsdts');
    }
}
