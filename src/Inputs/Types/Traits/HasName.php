<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasName {

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->getAttribute('name');
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName( string $name ) {
		$this->setAttribute('name',$name);

		return $this;
	}

}