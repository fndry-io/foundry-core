<?php

namespace Foundry\Core;

use Foundry\Core\Config\SettingRepository;
use Foundry\Core\Contracts\Repository;
//use Foundry\Core\Models\Listeners\SettingSaved;
use Foundry\Core\Providers\ConsoleServiceProvider;
use Foundry\Core\Providers\EventServiceProvider;
use Foundry\Core\Requests\FormRequestHandler;
//use Foundry\Core\View\Components\ViewComponentHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider {
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Booting the package.
	 */
	public function boot() {

		$this->registerNamespaces();
	}

	/**
	 * Register all modules.
	 */
	public function register() {
		$this->registerServices();
		$this->registerProviders();
	}

	/**
	 * Register package's namespaces.
	 */
	protected function registerNamespaces() {}

	/**
	 * Register the service provider.
	 */
	protected function registerServices() {

//		$this->app->singleton( Repository::class, function () {
//
//			if ( Cache::has( 'settings' ) ) {
//				$settings = Cache::get( 'settings' );
//			} else {
//				$settings = SettingSaved::getSettingsItems();
//				Cache::put( 'settings', $settings, now()->addDays( 30 ) );
//			}
//
//			return new SettingRepository( $settings );
//		} );
//
//		$this->app->alias( Repository::class, 'settings' );

		/**
		 * Register the FormRequestHandler Facade and link it to the FormRequestHandler Class
		 */
		$this->app->singleton( 'Foundry\Core\Facades\FormRequestHandler', function () {
			return new FormRequestHandler();
		} );
		$this->app->alias( 'Foundry\Core\Facades\FormRequestHandler', 'form-request-handler' );

	}

	/**
	 * Get the services provided by the provider.
	 *
	 */
	public function provides(): void {}

	/**
	 * Register providers.
	 */
	protected function registerProviders(): void {
		$this->app->register( ConsoleServiceProvider::class );
		$this->app->register( EventServiceProvider::class );
	}

}
