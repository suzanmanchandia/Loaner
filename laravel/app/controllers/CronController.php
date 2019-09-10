<?php

class CronController extends BaseController {

	/**
	 * Cron call
	 */
	public function nightly()
	{
		return Artisan::call('loaner:nightly');
	}
    
}