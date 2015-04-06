<?php namespace App\Models\Group\Host;

use App\Models\Base;

class Message extends Base 
{
	protected $table 	= 'downie_messages';

	public $timestamps 	= true;

	protected $fillable = [
		'host_id', 
		'receipt',
		'request',
		'title',
		'message',
		'priority'
	];

	protected $visible 	= [
		'host_id', 
		'receipt',
		'request',
		'title',
		'message',
		'priority',
		'sent_at',
		'created_at'
	];

	public static function boot()
	{
		parent::boot();

		Message::saving(function($message) 
		{
			if($message->hasChanged('request'))
			{
				$message->sent_at = $message->freshTimestamp();
			}
		});
	}

	public function host()
	{
		return $this->belongsTo('App\Models\Group\Host');
	}

	public function acknowledgements()
	{
		return $this->hasMany('App\Models\Group\Host\Message\Acknowledgement');
	}

	public function setAcknowledgedAtAttribute($value)
	{
		$this->attributes['acknowledged_at'] = $this->fromDateTime($value);
	}

	public function scopeAcknowledgeable($query)
	{
		$query->where('priority', 2)
		      ->whereNull('acknowledged_at');
	}
}