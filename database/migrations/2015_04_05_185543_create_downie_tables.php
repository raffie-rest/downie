<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownieTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('downie_groups', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('name');

			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('downie_hosts', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->unsignedInteger('group_id');

			$table->string('name')->nullable();
			$table->string('url', 255);

			$table->enum('status', ['UP', 'DOWN'])
				  ->nullable();

			$table->smallInteger('status_code')
			      ->nullable();

			$table->timestamp('status_at')
				  ->nullable();

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('group_id')
				  ->references('id')
				  ->on('downie_groups')
				  ->onDelete('cascade')
				  ->onUpdate('cascade');
		});

		Schema::create('downie_hosts_states', function(Blueprint $table)
		{
			$table->increments('id');

			$table->unsignedInteger('host_id');

			$table->enum('status', ['UP', 'DOWN'])
				  ->nullable();

			$table->timestamps();

			$table->foreign('host_id')
				  ->references('id')
				  ->on('downie_hosts')
				  ->onDelete('cascade')
				  ->onUpdate('cascade');
		});

		Schema::create('downie_messages', function(Blueprint $table)
		{
			$table->increments('id');

			$table->unsignedInteger('host_id');
			
			$table->string('remote_id')
				  ->nullable();

			$table->string('title');
			$table->text('message');
			$table->tinyInteger('priority');

			$table->string('request')
				  ->nullable();

			$table->string('receipt')
				  ->nullable();

			$table->timestamps();

			$table->timestamp('sent_at')
				  ->nullable();

			$table->timestamp('acknowledged_at')
				  ->nullable();

			$table->foreign('host_id')
				  ->references('id')
				  ->on('downie_hosts')
				  ->onDelete('cascade')
				  ->onUpdate('cascade');
		});

		Schema::create('downie_messages_acks', function(Blueprint $table)
		{
			$table->increments('id');

			$table->unsignedInteger('message_id');

			$table->string('request')
				  ->nullable();

			$table->string('acknowledged_by');

			$table->timestamp('acknowledged_at')
				  ->nullable();

			$table->timestamp('last_delivered_at')
				  ->nullable();

			$table->timestamp('expires_at')
				  ->nullable();

			$table->timestamp('called_back_at')
				  ->nullable();

			$table->foreign('message_id')
				  ->references('id')
				  ->on('downie_messages')
				  ->onDelete('cascade')
				  ->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		DB::statement('SET foreign_key_checks = 0');

		Schema::drop('downie_messages_acks');
		Schema::drop('downie_messages');
		Schema::drop('downie_hosts_states');
		Schema::drop('downie_hosts');
		Schema::drop('downie_groups');

		DB::statement('SET foreign_key_checks = 1');
	}

}
