<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FineDefaultsPk extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('fineDefault', function(Blueprint $table){
                $table->increments('id');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('fineDefault', function(Blueprint $table){
                $table->dropColumn('id');
            });
	}

}
