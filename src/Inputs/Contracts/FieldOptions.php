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
	 * @return array
	 */
	static function options(): array;

    /**
     * Get the label for the given value
     *
     * @param $value
     * @param null $default
     * @return mixed
     */
    static function getSelectedLabel($value, $default = null);

}
