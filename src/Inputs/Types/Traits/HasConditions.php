<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasConditions {

	public function setConditions( array $conditions ) {
		$this->setAttribute('conditions', $conditions);
		return $this;
	}

	public function addCondition( string $condition ) {
		$this->appendToAttribute('conditions', $condition);
		return $this;
	}

	public function getConditions() {
		return $this->getAttribute('conditions');
	}
}