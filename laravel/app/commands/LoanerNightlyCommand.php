<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoanerNightlyCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'loaner:nightly';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run loaner nightly.';

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
		// Cancel expired reservations
        $reservations = DB::table('loans')->where('status', '=', Loan::STATUS_RESERVED)->where('issue_date', '<', time())->update(array(
                'status' => Loan::STATUS_CANCELED
            ));
        $this->info(sprintf('%d reservations canceled.', $reservations));
        Log::info(sprintf('%d reservations canceled.', $reservations), array('Nightly Cron Log'));

        $loans = Loan::query()->active()->where('due_date', '<', time())->with('user')->get();
        /* @var $loans Loan[] */
        $fines = FineDefault::query()->lists('defaultFine', 'deptID');

        // Update fine
        foreach ($loans as $loan)
        {
            $loan->fine = $loan->fine + array_get($fines, $loan->deptID);
            $loan->save();

            $loan->user->calculateFine();
        }

        $this->info(sprintf('%d overdue loans fined.', $loans->count()));

		Log::info(sprintf('%d overdue loans fined.', $loans->count()), array('Nightly Cron Log'));
	}

}
