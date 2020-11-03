<?php

namespace Foundry\Core;

use Carbon\Carbon;
use Foundry\Core\Console\Commands\MakeModuleCommand;
use Illuminate\Support\Facades\Validator;
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

        Validator::extend('telephone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^\+[0-9]{1,15}$/', $value);
        });
        Validator::extend('valid_date', function ($attribute, $value, $parameters, $validator) {
            if ($value instanceof Carbon) {
                return true;
            } elseif (is_string($value)) {
                try {
                    $date = Carbon::createFromFormat($parameters[0], $value);
                    return true;
                } catch (\Throwable $e) {
                    return false;
                }
            }

            return false;
        });

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
