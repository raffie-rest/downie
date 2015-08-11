<?php

return [
	'hipchat_v1' 	=> [
		'data_type' => 'json',
		'room_id'	=> env('HIPCHAT_ROOM_ID', ''),
    'defaults'  => [
			'base_url'    => 'https://api.hipchat.com/v1',
			'defaults'	  => [
				'query'	 	  => [
					'auth_token' => env('HIPCHAT_TOKEN', ''),
					'format'	 	 => 'json'
				]
			]
    ]
	],
	'pushover_v1'	=> [
		'data_type' => 'json',
	    'defaults'  => [
			'base_url'    	=> 'https://api.pushover.net/1',
			'defaults'	  	=> [
				'query'	 	=> [
					'token' => env('PUSHOVER_TOKEN', ''),
					'user'  => env('PUSHOVER_USER', '')
				]
			]
	    ]
	]
];