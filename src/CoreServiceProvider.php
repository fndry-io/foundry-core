<?php

namespace Foundry\Core;

use Foundry\Core\Console\Commands\MakeModuleCommand;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider {
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Booting the package.
	 */
	public function boot() {
        $this->registerCommands();
	}

    /**
     * Registers the commands for this service provider
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->commands([
            MakeModuleCommand::class
        ]);
    }


}
