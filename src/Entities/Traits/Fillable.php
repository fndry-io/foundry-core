<?php

namespace Foundry\Core\Entities\Traits;

use Foundry\Core\Entities\Entity;

/**
 * Trait Fillable
 *
 * @package Foundry\Core\Traits
 */
trait Fillable {

	public function fill(array $params)
	{
		foreach ($params as $key => $value) {
			if ($this->isFillable($key)) {
				if ($this->$key instanceof Entity) {
					$this->$key->fill($value);
				} else {
					$this->__set($key, $value);
				}
			}
		}
	}

	public function isFillable($name)
	{
		if (isset($this->fillable)) {
			if ($this->fillable === true || in_array($name, $this->fillable)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

}