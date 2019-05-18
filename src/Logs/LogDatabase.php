<?php

namespace Foundry\Core\Logs;

use Monolog\Logger;

class LogDatabase {

	/**
	 * Create a custom Monolog instance.
	 *
	 * @param  array  $config
	 * @return \Monolog\Logger
	 */
	public function __invoke(array $config)
	{
		$logger = new Logger('database');
		$logger->pushHandler(new DatabaseHandler());
		return $logger;
	}
}