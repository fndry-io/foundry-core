<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasMinMax;


/**
 * Class EmailType
 *
 * @package Foundry\Requests\Types
 */
class EmailInputType extends InputType {

	use HasMinMax;

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		string $value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$type = 'email';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );

		$this->addRule('email');
	}
}
