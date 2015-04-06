<?php namespace App\Models\Group;

use App\Models\Base,
	Illuminate\Database\Eloquent\SoftDeletes;

use App\Exceptions\HostStatusChangedException;

use App\Models\Group\Host\State;

class Host extends Base 
{
	use SoftDeletes;

	protected $table 	= 'downie_hosts';

	public $timestamps 	= true;

	protected $fillable = [
		'group_id',
		'url', 
		'name',
		'status',
		'status_code'
	];

	protected $visible 	= [
		'group_id',
		'url', 
		'name',
		'status',
		'status_code',
		'status_at',
		'created_at',
		'updated_at',
		'deleted_at'
	];

	public static function boot()
	{
		parent::boot();

		Host::updating(function($host) 
		{
			if($host->hasChanged('status'))
			{
				$host->status_at = $host->freshTimestamp();
			}
		});
		Host::updated(function($host)
		{
			if($host->hasChanged('status'))
			{
				$host->states()->save(
					new State([
						'status'	=> $host->status
					])
				);

				throw new HostStatusChangedException($host);
			}
		});
	}

	public function current()
	{
		return $this->hasOne('App\Models\Group\Host\State')
					->orderBy('created_at', 'desc');
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group');
	}

	public function states()
	{
		return $this->hasMany('App\Models\Group\Host\State');
	}

	public function messages()
	{
		return $this->hasMany('App\Models\Group\Host\Message');
	}

	public function acknowledgements()
	{
		return $this->hasManyThrough('App\Models\Group\Host\Message\Acknowledgement', 'App\Models\Group\Host\Message');
	}
}