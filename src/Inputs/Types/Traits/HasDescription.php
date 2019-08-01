<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasDescription {

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->getAttribute('description');
	}

	/**
	 * @param string $description
	 *
	 * @return $this
	 */
	public function setDescription( string $description = null ) {
		$this->setAttribute('description', $description);

		return $this;
	}

}