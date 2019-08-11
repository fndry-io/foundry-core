<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Entities\Entity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasValue {

	public function __HasValue() {
		$this->setValue(null);
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		$value = $this->getAttribute('value');
		if ( method_exists($this, 'getName') && old( $this->getName() ) !== null ) {
			return old( $this->$this->getName() );
		} elseif ( $value !== null ) {
			return $value;
		} elseif ( isset($this->model) && method_exists($this, 'getName') ) {
			return $this->getModelValue( $this->getName() );
		} elseif ( isset($this->entity) && method_exists($this, 'getName') ) {
			return $this->getEntityValue( $this->getName() );
		}
		return null;
	}

	/**
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setValue( $value = null ) {
		if ($this instanceof Castable) {
			$value = $this->getCastValue($value);
		} else {
			if ($this instanceof IsMultiple && $this->isMultiple()) {
				if ($value) {
					$values = [];
					foreach ($value as $_value) {
						static::castValue($_value, $this->getCast());
						$values[] = $_value;
					}
					$value = $values;
				}
			} else {
				static::castValue($values, $this->getCast());
			}
		}
		$this->setAttribute('value', $value);
		return $this;
	}

	public function setDefault($value)
	{
		$this->setAttribute('default', $value);
		return $this;
	}

	public function getDefault()
	{
		return $this->getAttribute('default');
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

	private function getEntityValue( $name ) {
		$value = object_get( $this->entity, $name );
		if (is_object($value)) {
			if ($value instanceof Entity) {
				//todo correct this to the correct approach to getting the key
				$value = $value->getKey();
			} elseif ($value instanceof Collection) {
				//if the type is a checkbox, radio, select, then we need to display these and set their value accordingly
				if (in_array($this->type, ['checkbox', 'radio', 'select'])) {
					/**
					 * @var Collection $value
					 */
					foreach ($value as $key => $item) {
						if (is_object($item) && $item instanceof Entity) {
							//todo correct this to the correct approach to getting the key
							$value->offsetSet($key, $item->getKey());
						}
					}
				}
				$value = $value->toArray();
			}
		}
		return $value;
	}

	/**
	 * Cast the value to the given type
	 *
	 * This will also clean up the value to avoid incorrect castings, like "" or null to 0.
	 *
	 * @param $value
	 * @param $cast
	 */
	static public function castValue(&$value, $cast)
	{
		if ($value === "") {
			switch ($cast) {
				case 'bool':
				case 'boolean':
				case 'int':
				case 'integer':
				case 'float':
				case 'double':
					$value = null;
					break;
			}

		}
		if ($value === null) {
			return;
		}
		if ($cast === 'boolean' || $cast === 'bool') {
			if ($value === 'true' || $value === true) {
				$value = true;
			} else {
				$value = false;
			}
		}
		settype($value, $cast);
	}

}