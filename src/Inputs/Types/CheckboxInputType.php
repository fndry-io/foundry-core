<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Traits\HasOptions;

/**
 * Class CheckboxType
 *
 * @package Foundry\Requests\Types
 */
class CheckboxInputType extends InputType implements Choosable {

	use HasOptions;

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
		$type = 'switch';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );

		$this->setCheckedValue(true);
		$this->setUncheckedValue(false);
		$this->setAttribute('switch', true);
	}

	public function isChecked() {
		return $this->getValue() === $this->getCheckedValue();
	}

	public function setCheckedValue($value)
	{
		$this->setAttribute('checkedValue', $value);
		return $this;
	}

	public function getCheckedValue()
	{
		return $this->getAttribute('checkedValue');
	}

	public function setUncheckedValue($value)
	{
		$this->setAttribute('uncheckedValue', $value);
		return $this;
	}

	public function display($value = null) {

		if ($value === null) {
			$value = $this->getValue();
		}

		$options = $this->getOptions($value);

		if ( $value === '' || $value === null || ( $this->isMultiple() && empty( $value ) ) ) {
			return null;
		}

		if ( empty( $options ) ) {
			return $value;
		}

		//make sure it is an array
		if ( isset( $options[ $value ] ) ) {
			return $options[ $value ];
		} else {
			return $value;
		}
	}

}
