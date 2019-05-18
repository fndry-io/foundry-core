<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasValue {

	/**
	 * Value
	 *
	 * @var mixed $value
	 */
	protected $value = null;

	/**
	 * @var mixed the default value or values selected
	 */
	protected $default;

	/**
	 * @return string
	 */
	public function getValue() {
		if ( old( $this->name ) !== null ) {
			return old( $this->name );
		} elseif ( $this->value !== null ) {
			return $this->value;
		} elseif ( $this->hasModel() ) {
			return $this->getModelValue( $this->name );
		} else {
			return null;
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @return InputType
	 */
	public function setValue( $value = null ) {
		$this->value = $value;

		return $this;
	}


	public function setDefault($value)
	{
		$this->default = $value;
		return $this;
	}

	public function getDefault()
	{
		return $this->default;
	}


	public function isInvalid() {
		return $this->hasErrors();
	}

	private function getModelValue( $name ) {
		$value = object_get( $this->model, $name );
		if (is_object($value)) {
			if ($value instanceof Model) {
				$value = $value->getKey();
			} elseif ($value instanceof Collection) {
				//if the type is a checkbox, radio, select, then we need to display these and set their value accordingly
				if (in_array($this->type, ['checkbox', 'radio', 'select'])) {
					/**
					 * @var Collection $value
					 */
					foreach ($value as $key => $item) {
						if (is_object($item) && $item instanceof Model) {
							$value->offsetSet($key, $item->getKey());
						}
					}
				}
				$value = $value->toArray();
			}
		}
		return $value;
	}



}