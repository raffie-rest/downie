<?php namespace App\Models\Group\Host;

use Illuminate\Database\Eloquent\Model;

class State extends Model 
{
	protected $table 	= 'downie_hosts_states';

	public $timestamps 	= true;

	protected $fillable = [
		'host_id',
		'status'
	];

	protected $visible 	= [
		'host_id',
		'status', 
		'created_at'
	];

	public static function boot()
	{
		parent::boot();

		State::created(function($state) 
		{
			$state->host->status = $state->status;
			$state->host->save();
		});
	}

	public function host()
	{
		return $this->belongsTo('App\Models\Group\Host');
	}

	public function scopeCleanable($query)
	{
		$query->whereRaw('DATEDIFF(NOW(),created_at) > ?', [config('downie.state_max_days')]);
	}
}