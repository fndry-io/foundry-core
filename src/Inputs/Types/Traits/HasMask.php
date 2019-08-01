<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasMask {

	public function setMask( $mask = null ) {
		$this->setAttribute('mask', $mask);

		return $this;
	}

	public function getMask() {
		return $this->getAttribute('mask');
	}
}