<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInResultVietlottsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_vietlotts', function (Blueprint $table) {
            $table->string('jackpot')->after('gkk2')->nullable();
            $table->string('jackpot1')->after('jackpot')->nullable();
            $table->string('jackpot2')->after('jackpot1')->nullable();
            $table->string('g1')->nullable()->change();
            $table->string('g2')->nullable()->change();
            $table->string('g3')->nullable()->change();
            $table->string('gkk1')->nullable()->change();
            $table->string('gkk2')->nullable()->change();
            $table->string('number')->nullable()->after('vietlott_id')->nullable();
            $table->string('ticket')->nullable()->after('number');
            $table->index('ticket');
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
            $table->dropColumn(['jackpot', 'jackpot1', 'jackpot2', 'number', 'ticket']);
        });
    }
}
