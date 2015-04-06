<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Group\Host;

use App\Commands\CheckHostStatus;

use Queue;

class PingHosts extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ping:hosts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pings registered hosts';

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
		//
		$hostIds = $this->argument('host-ids');

		$this->info('Retrieving hosts...');

		$hosts = $this->retrieveHosts($hostIds);

		if(sizeof($hosts) == 0)
		{
			$this->error('No hosts found / registered');
			return;
		}

		foreach($hosts as $host)
		{
			$this->info('[' . $host->name . '] ' . $host->url);

			Queue::push(new CheckHostStatus($host));
		}
	}

	/**
	 * Return hosts based on the arguments
	 *
	 * Returns selected hosts, or all when empty
	 * 
	 * @param  string $hostIds
	 * 
	 * @return Collection
	 */
	protected function retrieveHosts($hostIds = '')
	{
		if( ! empty($hostIds))
		{
			$hosts = explode(',', $hostIds);

			$query = Host::query();

			$query->where('id', $hosts[0]);

			for($i = 1; $i < sizeof($hosts); $i++)
			{
				$query->orWhere('id', $hosts[$i]);
			}

			$hosts = $query->get();
		}
		if(empty($hostIds))
		{
			$hosts = Host::get();
		}

		return $hosts;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['host-ids', InputArgument::OPTIONAL, 'Comma-separated internal ids of the hosts to check. Checks all registered hosts if unspecified.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
