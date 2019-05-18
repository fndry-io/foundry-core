<?php

namespace Foundry\Core\Inputs\Contracts;

/**
 * Interface Options
 *
 * Allows us to define the available options for a field
 *
 * @package Foundry\Models
 */
interface FieldOptions {

	/**
	 * The input options
	 *
	 * @param \Closure $closure A query builder to modify the query if needed
	 * @param mixed $value
	 *
	 * @return array
	 */
	static function options( \Closure $closure = null, $value = null ): array;

}