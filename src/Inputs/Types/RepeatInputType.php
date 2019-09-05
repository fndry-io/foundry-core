<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class RepeatInputType
 *
 * @package Foundry\Requests\Types
 */
class RepeatInputType extends TextInputType {

	protected $cast = 'array';

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		$value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null,
		string $type = 'text'
	) {
		parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);
		$this->setType('repeat');
	}

	public function setDateInputName($name)
	{
		$this->setAttribute('dateInputName', $name);
		return $this;
	}

	public function getDateInputName()
	{
		return $this->getAttribute('dateInputName');
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
