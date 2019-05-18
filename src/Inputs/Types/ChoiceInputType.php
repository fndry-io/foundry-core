<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasOptions;

/**
 * Class ChoiceType
 *
 * @package Foundry\Requests\Types
 * @todo Update ChoiceType and others of a similar nature to rather use traits for the additional properties and methods
 */
class ChoiceInputType extends InputType implements Choosable {

	use HasOptions;
	use HasMinMax;

	protected $inline;

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		bool $expanded,
		bool $multiple,
		?array $options,
		$value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$this->setMultiple( $multiple );
		$this->setOptions( $options );
		$this->setExpanded( $expanded );
		$type = $expanded ? $multiple ? 'checkbox' : 'radio' : 'select';

		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
	}


	public function setInline($value = true)
	{
		$this->inline = $value;
		return $this;
	}

	public function getInline()
	{
		return $this->inline;
	}

	public function display($value = null) {

		if ($value === null) {
			$value = $this->getValue();
		}

		$options = $this->getOptions($value);

		if ( $value === '' || $value === null || ( $this->multiple && empty( $value ) ) ) {
			return null;
		}

		if ( empty( $options ) ) {
			return $value;
		}

		//make sure it is an array
		$value = (array) $value;
		$values = [];
		foreach ( $value as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$values[] = $options[ $key ];
			}
		}
		if ( count( $values ) === 1 ) {
			return $values[0];
		} else {
			return $values;
		}
	}
}
