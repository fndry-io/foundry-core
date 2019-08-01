<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasLabel {

	/**
	 * @return string
	 */
	public function getLabel(): string {
		return $this->getAttribute('label');
	}

	/**
	 * @param string $label
	 *
	 * @return $this
	 */
	public function setLabel( string $label = null ) {
		$this->setAttribute('label', $label);

		return $this;
	}

}