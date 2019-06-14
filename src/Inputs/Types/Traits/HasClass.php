<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasClass {

	public function setClass( $class = null ) {
		$this->setAttribute('class', $class);

		return $this;
	}

	public function getClass() {
		return $this->getAttribute('class');
	}
}