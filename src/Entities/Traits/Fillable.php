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
			if (!isset($this->fillable) || in_array($key, $this->fillable)) {
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