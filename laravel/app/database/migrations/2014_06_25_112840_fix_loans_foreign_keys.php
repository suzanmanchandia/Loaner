<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLoansForeignKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('loans', function(Blueprint $table) {
                $table->integer('userNum')->after('userid');
            });

        DB::table(DB::raw('loans ua'))->update(array(
                'ua.userNum' => DB::raw('(SELECT u.userNum from users u WHERE u.userid = ua.userid)')
            ));

        Schema::table('loans', function(Blueprint $table) {
                $table->foreign('userNum')->references('userNum')->on('users')->onDelete('cascade');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('loans', function(Blueprint $table) {
                $table->dropForeign('loans_usernum_foreign');
            });
        Schema::table('loans', function(Blueprint $table) {
                $table->dropColumn('userNum');
            });
	}

}
