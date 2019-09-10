<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoanerCleanupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'loaner:cleanup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clean up all kits and equipment incorrectly marked as on loan.';

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
		DB::beginTransaction();

		$kits = DB::table('kits AS k')->select('k.*')->leftJoin(DB::raw('(SELECT * FROM loans WHERE kitid <> \'\') AS l'), function($join){
			$join->on('l.kitid', '=', 'k.kitid');
			$join->on('l.status', 'in', DB::raw(sprintf('(%s)', join(',', array(Loan::STATUS_SHORT_TERM, Loan::STATUS_OWING, Loan::STATUS_LONG_TERM)))));
		})
			->whereNull('l.lid')
			->where('k.status', '=', Kit::STATUS_LOANED)->update(array(
				'k.status' => Kit::STATUS_ACTIVE
			));

		$equipment = DB::table('equipments AS e')
			->select('e.*')
			->leftJoin(DB::raw('(SELECT * FROM loans WHERE equipmentid <> \'\') AS l'), function($join){
				/* @var $join Illuminate\Database\Query\JoinClause */
				$join->on('l.equipmentid', '=', DB::raw('e.equipmentid OR (l.kitid = e.kitid)'));
				$join->on('l.status', 'in', DB::raw(sprintf('(%s)', join(',', array(Loan::STATUS_SHORT_TERM, Loan::STATUS_OWING, Loan::STATUS_LONG_TERM)))));
			})
			->whereNull('l.lid')
			->where('e.status', '=', Kit::STATUS_LOANED)
			->update(array(
					'e.status' => Kit::STATUS_ACTIVE
				)
			);

		Log::debug(print_r(DB::getQueryLog(), true));

		$this->info(sprintf('%d kits and %d equipment will be updated.', $kits, $equipment));

		if ($this->confirm('Do you wish to continue? [yes|no]', false))
		{
			$this->info('Kits and equipment updated.');
			DB::commit();
		}
		else
		{
			$this->info('Kits and equipment not updated.');
			DB::rollBack();
		}
	}

}
