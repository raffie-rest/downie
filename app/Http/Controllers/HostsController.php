<?php namespace App\Http\Controllers;

use App\Models\Group\Host;

class HostsController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$hosts = Host::with('current')->get();

		$enabled = config('downie.status_page', false);

		if($enabled)
		{
			return view('hosts')->with('hosts', $hosts);
		}

		return '';
	}
}