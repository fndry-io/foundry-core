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

	protected $checked;

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		string $value = null,
		bool $checked = false,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$type = 'switch';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );

		$this->setChecked( $checked );
	}

	public function isChecked() {
		return $this->checked;
	}

	public function setChecked( bool $checked = true ) {
		$this->checked = $checked;

		return $this;
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
		if ( isset( $options[ $value ] ) ) {
			return $options[ $value ];
		} else {
			return $value;
		}
	}

}
