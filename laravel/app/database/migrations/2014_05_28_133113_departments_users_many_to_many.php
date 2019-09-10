<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DepartmentsUsersManyToMany extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // Create departments users table
        Schema::create('departments_users', function(Blueprint $table) {
                $table->tinyInteger('deptID');
                $table->integer('userNum');
                $table->unique(array('deptID', 'userNum'));
                $table->timestamps();
            });
        // Add foreign keys
        Schema::table('departments_users', function(Blueprint $table) {
                $table->foreign('deptID')->references('deptID')->on('departments')->onDelete('cascade');
                $table->foreign('userNum')->references('userNum')->on('users')->onDelete('cascade');
            });
        // Build into department associations
        foreach (User::all() as $user)
        {
            $user->departments()->attach($user->deptID);
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('departments_users');
	}

}
