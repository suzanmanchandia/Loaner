<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FinePaymentsForeignKeys extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finePayments', function(Blueprint $table) {
                $table->integer('userNum')->after('userid');
            });

        DB::table(DB::raw('finePayments ua'))->update(array(
                'ua.userNum' => DB::raw('(SELECT u.userNum from users u WHERE u.userid = ua.userid)')
            ));

        Schema::table('finePayments', function(Blueprint $table) {
                $table->index('userNum');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finePayments', function(Blueprint $table) {
                $table->dropColumn('userNum');
            });
    }

}
