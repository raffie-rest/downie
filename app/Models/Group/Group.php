<?php namespace App\Models;

use App\Models\Base,
	Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Base 
{
	use SoftDeletes;

	protected $table 	= 'downie_groups';

	public $timestamps 	= true;

	protected $fillable = [
		'name'
	];

	protected $visible 	= [
		'name'
	];

	public function hosts()
	{
		return $this->hasMany('App\Models\Group\Host');
	}

	public function messages()
	{
		return $this->hasManyThrough('App\Models\Group\Host\Message', 'App\Models\Group\Host');
	}
}