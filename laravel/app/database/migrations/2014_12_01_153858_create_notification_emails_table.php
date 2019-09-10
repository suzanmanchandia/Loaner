<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department_emails', function(Blueprint $table){
			$table->increments('id');
			$table->tinyInteger('deptID');
			$table->string('email')->unique();
			$table->foreign('deptID')->references('deptID')->on('dept')->onDelete('cascade');
			$table->engine = 'Innodb';
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('department_emails');
	}

}
