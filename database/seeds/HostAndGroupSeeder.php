<?php

use Illuminate\Database\Seeder;

use App\Models\Group,
	App\Models\Group\Host;

class HostAndGroupSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Seedings watch groups and hosts...');

		$group = new Group;

		$group->fill([
			'name'			=> 'Generic list'
		]);

		$group->save();

		$hosts = [
			[
				'name'	=> 'Google Netherlands',
				'url'	=> 'http://www.google.nl/'
			],
			[
				'name'	=> 'Laravel',
				'url'	=> 'http://laravel.com/'
			],
			[
				'name'	=> 'Forge',
				'url'	=> 'https://forge.laravel.com/'
			]
		];

		foreach($hosts as $host)
		{
			$group->hosts()->save(new Host($host));
		}
	}
}