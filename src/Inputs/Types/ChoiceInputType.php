<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Entities\Contracts\IsEntity;
use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasOptions;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\Core\Inputs\Types\Traits\HasQueryOptions;
use Foundry\Core\Inputs\Types\Traits\HasTaggable;

/**
 * Class ChoiceType
 *
 * @package Foundry\Requests\Types
 */
class ChoiceInputType extends InputType implements Choosable, IsMultiple {

	use HasButtons;
	use HasOptions;
	use HasMinMax;
	use HasQueryOptions;
	use HasParams;
	use HasTaggable;

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		array $options = [],
		bool $expanded = false,
		bool $multiple = false,
		$value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$this->setValueKey('value');
		$this->setTextKey('text');

		$this->setMultiple( $multiple );
		$this->setOptions( $options );
		$this->setExpanded( $expanded );
		$type = $expanded ? $multiple ? 'checkbox' : 'radio' : 'select';

		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );

	}

	public function getValue()
    {
        $value = parent::getValue();
        if ($value instanceof IsEntity) {
            $value = object_get($value, $this->getValueKey());
        }
        return $value;
    }

    public function setValue( $value = null ) {
		if ($value instanceof IsEntity) {
			$value = $value->getKey();
		}
		return parent::setValue($value);
	}

	public function setInline(bool $value = true)
	{
		$this->setAttribute('inline', $value);
		return $this;
	}

	public function isInline()
	{
		$this->getAttribute('inline', false);
	}

	public function setSearchable($value = null)
	{
		$this->setAttribute('searchable', $value);
		return $this;
	}

	public function getSearchable()
	{
		return $this->getAttribute('searchable');
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
