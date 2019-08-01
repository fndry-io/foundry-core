<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasConditions {

	public function setConditions( array $conditions ) {
		$this->setAttribute('conditions', $conditions);
		return $this;
	}

	/**
	 * @param string $field The field that needs to be checked
	 * @param string $operator The operator for the condition. Currently supports ==,!==,>=,<=.
	 * @param null|string|int|array $value The value or possible values to check for
	 *
	 * @return $this
	 */
	public function addCondition( string $field, string $operator, $value = null ) {

		$values = (array) $value;
		foreach($values as $key => $val) {
			if ($val === null) {
				$values[$key] = "null";
			} elseif ($val === true) {
				$values[$key] = "true";
			} elseif ($val === false) {
				$values[$key] = "false";
			} elseif (is_int($val)) {
				$values[$key] = $val;
			} else {
				$values[$key] = "'$val'";
			}
		}
		$condition = "$field:$operator:" . implode(',', $values);

		$this->appendToAttribute('conditions', $condition);
		return $this;
	}

	public function getConditions() {
		return $this->getAttribute('conditions');
	}
}