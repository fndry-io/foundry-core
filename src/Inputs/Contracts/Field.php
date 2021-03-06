<?php

namespace Foundry\Core\Inputs\Contracts;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Entities\Entity;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface Field
 *
 * Allows us to define the HTML input, validation rule and cast type for a field
 *
 * @package Foundry\Models
 */
interface Field {

	/**
	 * The input type for displaying on a page
	 *
	 * @return self|Inputable
	 */
	static function input(): Inputable;

}
