<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasPosition {

	/**
	 * @return string
	 */
	public function getPosition(): string {
		return $this->getAttribute('position');
	}

	/**
	 * @param string $position
	 *
	 * @return $this
	 */
	public function setPosition( string $position = null ) {
		$this->setAttribute('position', $position);

		return $this;
	}

}