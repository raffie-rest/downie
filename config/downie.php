<?php

return [
	
	// The maximum number of days host state data is kept
	
	'state_max_days'	=> 180,

	// Whether or not to display the root status page
	
	'status_page'		  => true,

  // Can be pushover_v1 or hipchat_v1; make sure you set the auth data in the .env

  'methods'         => ['hipchat_v1', 'pushover_v1']
];