<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Raffie\REST\Adapter\Adapters\PushOver\v1\Receipt,
	App\Models\Group\Host\Message,
	App\Models\Group\Host\Message\Acknowledgement;

class CheckReceipt extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	/**
	 * Message
	 * 
	 * @var Message
	 */
	protected $message = false;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Message $message)
	{
		//
		$this->message = $message;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		//
		$receipt = Receipt::retrieve($this->message->receipt);

		$ack = Acknowledgement::updateOrCreate([
			'message_id'		=> $this->message->id,
			'acknowledged_by'	=> $receipt['acknowledged_by'],
			'request'			=> $receipt['request']
		], $receipt);
	}
}
