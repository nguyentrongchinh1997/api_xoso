<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInResultVietlottsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_vietlotts', function (Blueprint $table) {
            $table->string('g4')->after('g3')->nullable();
            $table->string('g5')->after('g4')->nullable();
            $table->string('g6')->after('g5')->nullable();
            $table->string('g7')->after('g6')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_vietlotts', function (Blueprint $table) {
            $table->dropColumn(['g4', 'g5', 'g6', 'g7']);
        });
    }
}
