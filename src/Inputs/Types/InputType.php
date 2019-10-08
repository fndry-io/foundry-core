<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasAutocomplete;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasErrors;
use Foundry\Core\Inputs\Types\Traits\HasForm;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasMask;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasPlaceholder;
use Foundry\Core\Inputs\Types\Traits\HasReadonly;
use Foundry\Core\Inputs\Types\Traits\HasRequired;
use Foundry\Core\Inputs\Types\Traits\HasRules;
use Foundry\Core\Inputs\Types\Traits\HasValue;
use Foundry\Core\Inputs\Types\Traits\HasSortable;

/**
 * Class Type
 *
 * @package Foundry\Requests\Types
 */
abstract class InputType extends BaseType implements Inputable {

	use HasButtons,
		HasId,
		HasLabel,
		HasValue,
		HasRules,
		HasClass,
		HasName,
		HasRequired,
		HasPlaceholder,
		HasHelp,
		HasReadonly,
		HasErrors,
		HasSortable,
		HasMask,
		HasAutocomplete,
		HasForm
	;

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		$value = null, //todo remove this
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null,
		string $type = 'text'
	) {
		parent::__construct();

		$this->setName( $name );
		$this->setType( $type );
		$this->setRequired( $required );

		$this->setLabel( $label ? $label : $name );
		$this->setRules( $rules );

		$this->setId( $id );
		$this->setPlaceholder( $placeholder ? $placeholder : $label ? $label : $name );
	}

	/**
	 * Json serialise field
	 *
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		$field = parent::jsonSerialize();

		//set the rules
		if ( $field['rules'] ) {
			$_rules = [];
			$rules  = $this->getRules();
			if ( $rules ) {
				foreach ( $rules as $rule ) {
					if ( $rule instanceof \Closure ) {
						continue;
					}
					if ( is_object( $rule ) ) {
						$_rules[] = (string) $rule;
					} elseif ( is_string( $rule ) ) {
						if (strpos($rule, 'exists:') || $rule === 'file') {
							continue;
						}
						$_rules[] = $rule;
					}
				}
				$_rules = implode( '|', $_rules );
			}
			$field['rules'] = $_rules;
		}

		if (!empty($this->buttons)) {
			$field['buttons'] = [];
			foreach ($this->buttons as $button) {
				$field['buttons'][] = $button->toArray();
			}
		}

		//set the fillable etc values
		foreach (
			[
				//'fillable' => 'isFillable',
				'visible'  => 'isVisible',
				'hidden'   => 'isHidden'
			] as $key => $method
		) {
			$field[ $key ] = call_user_func( [ $this, $method ] );
		}

		return $field;
	}

	public function getValue()
	{
		if ($this->form) {
			return $this->getForm()->getValue($this->getName());
		}
		return null;
	}

	public function setValue($value)
	{
		if ($this->form) {
			$this->getForm()->setValue($this->getName(), $value);
		}
		return $this;
	}

	public function isVisible()
	{
		if ($this->getForm()) {
			return $this->getForm()->isVisible($this->getName());
		}
		return true;
	}

	public function isHidden()
	{
		if ($this->getForm()) {
			return $this->getForm()->isHidden($this->getName());
		}
		return false;
	}

	public function display($value = null) {
		$value = $this->getValue();
		return $value;
	}
}
