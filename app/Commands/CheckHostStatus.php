<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use App\Exceptions\HostStatusChangedException;

use App\Models\Group\Host;

use GuzzleHttp\Client,
	GuzzleHttp\Exception\RequestException;

use Queue;

use App\Commands\SendPushMessage;

class CheckHostStatus extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	protected $host 	= false;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Host $host)
	{
		//
		$this->host = $host;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		//
		$this->performPing();
	}

	/**
	 * Perform host ping vis-a-vis Guzzle
	 * 
	 * @return void
	 */
	protected function performPing()
	{
		try
		{
			$client = new Client();

			$response = $client->get($this->host->url);

			$status 		= 'UP';
			$statusCode 	= $response->getStatusCode();
		}
		catch(RequestException $e)
		{
			$status 		= 'DOWN';
			$statusCode 	= $e->hasResponse() ? $e->getResponse()->getStatusCode() : 404;
		}
		finally
		{
			try
			{
				$this->host->status_code   = $statusCode;
				$this->host->status 	   = $status;
				$this->host->save();
			}
			catch(HostStatusChangedException $e)
			{
				Queue::push(new SendPushMessage($e));
			}
		}
	}
}