<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Raffie\REST\Adapter\Adapters\PushOver\v1\Message as PushOverMessage;

use App\Exceptions\HostStatusChangedException;

use App\Models\Group\Host\Message;

class SendPushMessage extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	/**
	 * Exception message
	 * 
	 * @var string
	 */
	protected $textMessage = '';

	/**
	 * Host passed by the exception
	 * 
	 * @var boolean
	 */
	protected $host 	 = false;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(HostStatusChangedException $e)
	{
		//$this->textMessage = $e->getMessage();
		$this->textMessage = $e->getMessage();
		$this->host 	   = $e->host;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$originalMessage = $this->generateMessage();

		$returnedMessage = PushOverMessage::send($originalMessage);

		$this->processMessage($originalMessage, $returnedMessage);
	}

	/**
	 * Saves the returned message to DB
	 * 
	 * @param  array  $originalMessage 
	 * @param  array  $returnedMessage 
	 * 
	 * @return void                  
	 */
	protected function processMessage(array $originalMessage, array $returnedMessage)
	{
		$newMessage = [
			'title'		=> $originalMessage['title'],
			'message'	=> $originalMessage['message'],
			'priority'	=> $originalMessage['priority']
		];

		if(array_key_exists('request', $returnedMessage)) $newMessage['request'] = $returnedMessage['request'];
		if(array_key_exists('receipt', $returnedMessage)) $newMessage['receipt'] = $returnedMessage['receipt'];

		$this->host->messages()->save(new Message($newMessage));
	}

	/**
	 * Generates pushover message based on UP/DOWN condition
	 * 
	 * @return array message
	 */
	protected function generateMessage()
	{
		$messageData = [
			'title'		=> 'Host status changed',
			'message'	=> trim($this->textMessage),
			'url'		=> $this->host->url,
			'url_title'	=> $this->host->name
		];

		$additionals = [
			'priority'	=> 1,
			'sound'		=> 'bike'
		];

		if($this->host['status'] == 'DOWN')
		{
			$additionals = [
				'priority'	=> 2,
				'sound'		=> 'siren',
				'retry'		=> 300,
				'expire'	=> 1800
			];

			$callBackURL = config('rest_resources.pushover_v1.callback', '');

			if( ! empty($callBackUrl))
			{
				$additionals['callback'] = $callBackUrl;
			}
		}

		return array_merge($messageData, $additionals);
	}
}
