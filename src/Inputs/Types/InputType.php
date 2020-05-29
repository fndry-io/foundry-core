<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasAutocomplete;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasErrors;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasInputs;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasMask;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasPlaceholder;
use Foundry\Core\Inputs\Types\Traits\HasReadOnly;
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
		HasReadOnly,
		HasErrors,
		HasSortable,
		HasMask,
		HasAutocomplete,
        HasInputs
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
	}

	/**
	 * Json serialise field
	 *
	 * @return array
	 */
	public function jsonSerialize(): array
	{
        if (!$this->getPlaceholder()) {
            $this->setPlaceholder( $this->getLabel() );
        }
		$field = parent::jsonSerialize();

		//set the rules
		if ( $field['rules'] || $field['required'] ) {
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
		if ($this->inputs) {
			return $this->getInputs()->getValue($this->getName());
		}
		return null;
	}

	public function setValue($value)
	{
		if ($this->inputs) {
			$this->getInputs()->setValue($this->getName(), $value);
		}
		return $this;
	}

	public function isVisible()
	{
		if ($this->inputs) {
			return $this->getInputs()->isVisible($this->getName());
		}
		return true;
	}

	public function isHidden()
	{
		if ($this->inputs) {
			return $this->getInputs()->isHidden($this->getName());
		}
		return false;
	}

	public function setDisabled(bool $disabled = true)
    {
        $this->setAttribute('disabled', $disabled);
        return $this;
    }

    public function getDisabled()
    {
        $this->getAttribute('disabled');
    }

	public function display($value = null) {
		$value = $this->getValue();
		return $value;
	}

    /**
     * Adds an instruction to append text to the input
     *
     * @param $text
     * @return $this
     */
    public function setAppend($text){
        $this->setAttribute('append', $text);
        return $this;
    }

    /**
     * Adds an instruction to append text to the input
     *
     * @param $text
     * @return $this
     */
    public function setPrepend($text){
        $this->setAttribute('prepend', $text);
        return $this;
    }
}
