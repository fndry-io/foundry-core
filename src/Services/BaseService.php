<?php

namespace Foundry\Core\Services;

class BaseService {

	/**
	 * @return static
	 */
	static function service()
	{
		return app(static::class);
	}
}