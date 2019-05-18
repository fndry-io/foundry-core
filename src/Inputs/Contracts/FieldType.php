<?php

use Foundry\Core\Inputs\Types\Contracts\Inputable;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface Field
 *
 * Allows us to define the HTML input, validation rule and cast type for a field
 *
 * @package Foundry\Models
 */
interface FieldType {

	/**
	 * The input type for displaying on a page
	 *
	 * @param Model $model
	 *
	 * @return Inputable
	 */
	static function input( Model &$model = null ): Inputable;

}