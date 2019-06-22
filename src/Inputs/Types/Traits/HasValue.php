<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Entities\Entity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasValue {

	public function HasValue() {
		$this->setValue(null);
	}

	/**
	 * @return string
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



}