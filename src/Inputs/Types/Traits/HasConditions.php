<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait HasConditions {

	/**
	 * @var array
	 */
	protected $conditions = [];

	public function setConditions( array $conditions ) {
		$this->conditions = $conditions;
		return $this;
	}

	public function addCondition( string $condition ) {
		$this->conditions[] = $condition;
		return $this;
	}

	public function getConditions() {
		return $this->conditions;
	}
}