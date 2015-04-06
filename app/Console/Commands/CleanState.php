<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Group\Host\State;

class CleanState extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'clean:state';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cleans the host state table.';

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
		$states = State::cleanable()->get();

		if( ! $states)
		{
			$this->error('Noting to clean up');
			return;
		}

		foreach($states as $state)
		{
			$state->forceDelete();
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
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
