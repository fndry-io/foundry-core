<?php

namespace Foundry\Core\Support;

use Illuminate\Support\ServiceProvider as BaseServiceProvder;


class ServiceProvider extends BaseServiceProvder {


	/**
	 * Register a path to the Doctrine paths where entity definitions can be found
	 *
	 * @param string|array $path
	 * @param string $key Defaults to 'doctrine.managers.default.paths'
	 */
	protected function mergeDoctrinePaths($path, $key = 'doctrine.managers.default.paths')
	{
		$paths = (array) $path;
		$this->app['config']->set($key, array_merge($paths, $this->app['config']->get($key, [])));
	}

}