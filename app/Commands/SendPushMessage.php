<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Raffie\REST\Adapter\Adapters\PushOver\v1\Message as PushOverMessage,
		Raffie\REST\Adapter\Adapters\HipChat\v1\Message  as HipChatMessage;

use App\Exceptions\HostStatusChangedException,
		InvalidArgumentException;

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
	 * The pushover notification types to handle
	 * 
	 * @var array
	 */
	protected $methods = '';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(HostStatusChangedException $e)
	{
		//$this->textMessage = $e->getMessage();
		$this->textMessage = $e->getMessage();
		$this->host 	   	 = $e->host;
		$this->methods 		 = config('downie.methods', []);

		$this->validateMethods();
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (in_array('pushover_v1', $this->methods)) {
			$originalMessage = $this->generatePushoverMessage();
			$returnedMessage = PushOverMessage::send($originalMessage);

			$this->processPushoverMessage($originalMessage, $returnedMessage);			
		}
		if (in_array('hipchat_v1', $this->methods)) {
			$originalMessage = $this->generateHipchatMessage();
			$returnedMessage = HipChatMessage::send($originalMessage);

			$this->processHipchatMessage($originalMessage, $returnedMessage);
		}
	}

	/**
	 * Validate configured message options
	 * - Throws InvalidArgumentException on fail
	 * 
	 * @return void
	 */
	protected function validateMethods()
	{
		if ( ! is_array($this->methods)) {
			throw new InvalidArgumentException('The downie.methods config must be an array');
		}
		if (empty($this->methods)) {
			throw new InvalidArgumentException('No message methods specified');
		}

		foreach ($this->methods as $method) {
			if ( ! config('rest_resources.' . $method, false)) {
				throw new InvalidArgumentException('Invalid method ' . $method . ' specified');
			}
		}
	}

	/**
	 * Saves the returned hipchat payload to DB
	 * 
	 * @param  array  $originalMessage 
	 * @param  array  $returnedMessage 
	 * 
	 * @return void                  
	 */
	protected function processHipchatMessage(array $originalMessage, array $returnedMessage)
	{
		\Log::info(print_r($returnedMessage, true));
	}

	/**
	 * Saves the returned pushover payload to DB
	 * 
	 * @param  array  $originalMessage 
	 * @param  array  $returnedMessage 
	 * 
	 * @return void                  
	 */
	protected function processPushoverMessage(array $originalMessage, array $returnedMessage)
	{
		$newMessage = [
			'title'			=> $originalMessage['title'],
			'message'		=> $originalMessage['message'],
			'priority'	=> $originalMessage['priority']
		];

		if(array_key_exists('request', $returnedMessage)) $newMessage['request'] = $returnedMessage['request'];
		if(array_key_exists('receipt', $returnedMessage)) $newMessage['receipt'] = $returnedMessage['receipt'];

		$this->host->messages()->save(new Message($newMessage));
	}

	/**
	 * Generates hipchat message based on UP/DOWN condition
	 * 
	 * @return array message
	 */
	protected function generateHipchatMessage()
	{
		$messageData = [
			'from'						=> 'Downie',
			'title'						=> 'Host status changed',
			'message'					=> trim($this->textMessage),
			'message_format'	=> 'text',
			'notify'					=> 1,
			'color'						=> $this->host['status'] == 'DOWN' ? 'red' : 'green',
			'room_id'					=> config('rest_resources.hipchat_v1.room_id')
		];

		return $messageData;
	}

	/**
	 * Generates pushover message based on UP/DOWN condition
	 * 
	 * @return array message
	 */
	protected function generatePushoverMessage()
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
