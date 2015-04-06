<?php namespace App\Models\Group\Host\Message;

use Illuminate\Database\Eloquent\Model;

class Acknowledgement extends Model 
{
	protected $table 	= 'downie_messages_acks';

	public $timestamps 	= false;

	protected $fillable = [
		'message_id',
		'acknowledged_by',
		'acknowledged_at',
		'last_delivered_at',
		'expires_at',
		'called_back_at',
		'request'
	];

	protected $visible 	= [
		'message_id',
		'acknowledged_by',
		'acknowledged_at',
		'last_delivered_at',
		'expires_at',
		'called_back_at',
		'request'
	];

	public static function boot()
	{
		parent::boot();

		Acknowledgement::saved(function($ack)
		{
			$ack->message->acknowledged_at = $ack->acknowledged_at;
			$ack->message->save();
		});
	}

	public function message()
	{
		return $this->belongsTo('App\Models\Group\Host\Message');
	}

	public function setAcknowledgedAtAttribute($value)
	{
		$this->attributes['acknowledged_at'] = $this->fromDateTime($value);
	}

	public function setLastDeliveredAtAttribute($value)
	{
		$this->attributes['last_delivered_at'] = $this->fromDateTime($value);
	}

	public function setExpiresAtAttribute($value)
	{
		$this->attributes['expires_at'] = $this->fromDateTime($value);
	}

	public function setCalledBackAtAttribute($value)
	{
		$this->attributes['called_back_at'] = $this->fromDateTime($value);
	}
}