<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoanerUpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'loaner:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run scripts to update code.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		if (App::environment('local'))
		{
			$this->call('ide-helper:generate');
			$this->call('debugbar:publish');
		}
	}

}
