<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasOptions;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\Core\Inputs\Types\Traits\HasQueryOptions;
use Foundry\Core\Inputs\Types\Traits\HasTaggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
        if ($value instanceof Model) {
            $value = object_get($value, $this->getValueKey());
        } elseif ($value instanceof Collection) {
            $value = $value->pluck($this->getValueKey())->toArray();
        }
        return $value;
    }

    public function setValue( $value = null ) {
		if ($value instanceof Model) {
			$value = $value->getKey();
		} elseif ($value instanceof Collection) {
            $value = $value->pluck($this->getValueKey())->toArray();
        }
		return parent::setValue($value);
	}

	public function setInline(bool $value = true)
	{
		$this->setAttribute('inline', $value);
		return $this;
	}

	public function setButtonType(string $type)
    {
        $this->setAttribute('button_type', $type);

        return $this;
    }

    public function getButtonType()
    {
        return $this->getAttribute('button_type', null);
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

	public function setStacked($value)
    {
        $this->setAttribute('stacked', $value);
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

    /**
     * @param mixed $value
     * @param null $default
     * @return array|mixed|null
     */
	static function getSelectedLabel($value, $default = null)
    {
        if (is_array($value)) {
            $_options = [];
            foreach ($value as $_value) {
                $_option = static::getSelectedLabel( $_value, null);
                if ($_option !== null) {
                    $_options[] = $_option;
                }
            }
            if (!empty($_options)) {
                return $_options;
            }
            return $default;
        }

        /** @var array $options */
        $options = Cache::remember('options::' . static::class, 30, function(){
            /** @var HasOptions|ChoiceInputType $input */
            $input = static::input();
            return \Illuminate\Support\Arr::pluck(static::options(), $input->getTextKey(), $input->getValueKey());
        });
        return ($options[$value]) ?? $default;
    }
}
