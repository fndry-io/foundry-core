<?php

namespace Foundry\Core\Entities\Traits;

/**
 * Trait Fillable
 *
 * @package Foundry\Core\Traits
 */
trait Fillable {

	public function fill($params)
	{
		foreach ($params as $key => $value) {
			if ($this->isFillable($key)) {
				$this->$key = $value;
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