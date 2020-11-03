<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasValue {

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
