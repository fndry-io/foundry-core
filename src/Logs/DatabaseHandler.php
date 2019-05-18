<?php

namespace Foundry\Core\Logs;

use Foundry\Core\Events\LogEvent;
use Foundry\Core\Models\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DatabaseHandler extends AbstractProcessingHandler
{

	public function __construct( int $level = Logger::DEBUG, bool $bubble = true ) {
		parent::__construct( $level, $bubble );
		//Add the extra processor
		$this->pushProcessor(new ExtraProcessor());
	}

	/**
	 * {@inheritDoc}
	 */
	protected function write(array $record)
	{
		try {
			Log::saveRecord($record);
		} catch (\Exception $e) {
			//ignore the exception as this will go to the normal log
		}

		// Queue implementation
		//event(new LogEvent($record));
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter()
	{
		return new DatabaseFormatter();
	}

}