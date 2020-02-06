<?php

namespace Foundry\Core\Providers;

use Foundry\Core\Events\SettingSaved;
use Foundry\Core\Listeners\SettingSaved as SettingSavedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		SettingSaved::class => [
			SettingSavedListener::class,
		],
	];

	/**
	 * Register any other events for your application.
	 * @return void
	 */
	public function boot() {
		parent::boot();

	}
}
