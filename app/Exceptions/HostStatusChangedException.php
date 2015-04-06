<?php namespace App\Exceptions;

use Exception;

class HostStatusChangedException extends Exception 
{
	public $host = false;

    public function __construct(\App\Models\Group\Host $host, $code = 0, Exception $previous = null)
    {
    	$this->host = $host;

    	$error = 'Host ' . $host->status . " ( HTTP " . $host->status_code . ")\n" . $host->url . "\n" . $host->status_at;

        // Readable error message ($error) is accessible through "getMessage()"
        parent::__construct($error, $code, $previous);
    }
}