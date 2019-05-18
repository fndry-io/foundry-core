<?php

namespace Foundry\Core\Inputs\Types;


/**
 * Class HiddenType
 *
 * @package Foundry\Requests\Types
 */
class HiddenInputType extends InputType {

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
		$type = 'hidden';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
	}
}
