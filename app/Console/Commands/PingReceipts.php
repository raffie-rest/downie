<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Group\Host\Message,
	App\Models\Group\Host\Message\Acknowledgement,
	Raffie\REST\Adapter\Adapters\PushOver\v1\Receipt;

use Queue,
	App\Commands\CheckReceipt;

class PingReceipts extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ping:receipts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieves a PushOver message receipt.';

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
		$receipt = $this->argument('receipt');

		$this->info('Retrieving receiptable messages...');

		if( ! empty($receipt))
		{
			$messages = Message::where('receipt', $receipt)
							   ->acknowledgeable()
							   ->get();
		}
		else
		{
			$messages = Message::whereNull('acknowledged_at')
							   ->acknowledgeable()
							   ->get();
		}

		if( ! $messages)
		{
			$this->error('No messages found');
			return;
		}	

		foreach($messages as $message)
		{
			$this->info('Checking acknowledgement of message #' . $message->id);
			Queue::push(new CheckReceipt($message));
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['receipt', InputArgument::OPTIONAL, 'Specify the message receipt. Pull all receipted message without acknowledged_at if unspecified.'],
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
