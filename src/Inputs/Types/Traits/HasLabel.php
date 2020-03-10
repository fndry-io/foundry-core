<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasLabel {

	/**
	 * @return null|string
	 */
	public function getLabel() {
		return $this->getAttribute('label');
	}

	/**
	 * @param null|string $label
	 *
	 * @return $this
	 */
	public function setLabel( ?string $label = null ) {
		$this->setAttribute('label', $label);

		return $this;
	}

}
