# Downie
Laravel 5 Up Checker with Pushover notifications

## Concerning this project

Although the correlation in name is evident, and some might take that in the pejorative sense, it is not meant as such, it mainly being an anglicisation of the Gaelic Maol Dòmhnaich, which rougly translates as ["son of the servant of the Lord (Sunday)"](http://en.wikipedia.org/wiki/Downie). 

It's a rudimentary Up Checker based on L5 / beanstalkd / scheduling and the `adapter` project for push notifications. Although feedback is welcome; I don't consider it fit for production purposes, so do that at your own peril.

## Getting started

After cloning the project and installing the dependencies, run:

    php artisan migrate && php artisan db:seed
  
It christens the DB with a generic group and stub host list.

### Enable beanstalkd

In homestead, do:

    sudo service beanstalkd start

In your project folder, run:

    php artisan queue:listen --tries=2
  
[Read more about L5 Queues](http://laravel.com/docs/5.0/queues)

### Configs

In `config/rest_resources.php`, set your PushOver auth:

			'base_url'    	=> 'https://api.pushover.net/1',
			'defaults'	  	=> [
				'query'	 	=> [
					'token' => 'foo',    // app token
					'user'  => 'bar'     // group or user token
				]
			]

If you look at `config/downie.php`:

    // The maximum number of days host state data is kept
    
    'state_max_days'	=> 180,
    
    // Whether or not to display the root status page
    
    'status_page'		=> true
	
The latter meaning that you can browse to your project homestead root in the browser and view the status of your hosts. Setting it to `false` means a blank HTTP 200.

A cleanup utility is included that swipes the `downie_hosts_states` monthly. State records older than `state_max_days` get purged.

### Enabling the scheduler

Append it to your crontab:

    * * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1

Of course, individual commands can also be cronned separately.

[Read more about L5 Artisan CLI](http://laravel.com/docs/master/artisan)

## Command list

    php artisan clean:state
  
Cleans the host states in accordance with aforementioned `state_max_days` setting. Runs weekly.

    php artisan ping:hosts
  
By default, pings all registered hosts for availability. Optionally, a comma-separated list of internal ids may be passed as an argument. Runs by the minute.

    php artisan ping:receipts
  
By default, checks all sent and unacknowledged messages of emergency priority for acknowledgement. Optionally, a comma-separated list of receipts may be passed as an argument. Runs hourly.
