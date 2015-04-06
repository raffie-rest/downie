<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\PingHosts',
		'App\Console\Commands\PingReceipts',
		'App\Console\Commands\CleanState'
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('ping:receipts')
				 ->withoutOverlapping()
				 ->hourly();

		$schedule->command('ping:hosts')
				 ->withoutOverlapping()
				 ->cron('* * * * *');

		$schedule->command('clean:state')
				 ->withoutOverlapping()
				 ->weekly();
	}
}