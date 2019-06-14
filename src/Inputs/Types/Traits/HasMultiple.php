<?php

namespace Foundry\Core\Inputs\Types\Traits;

/**
 * Trait HasMultiple
 *
 * Used to determine if we allow multiple selections (collections, chechbox vs radio, or select vs select[multiple]
 *
 * @package Foundry\Core\Inputs\Types\Traits
 */
trait HasMultiple {

	/**
	 * @return bool
	 */
	public function getMultiple(): bool {
		return $this->getAttribute('multiple', false);
	}

	/**
	 * @param bool $multiple
	 *
	 * @return $this
	 */
	public function setMultiple( bool $multiple = null ) {
		$this->setAttribute('multiple', $multiple);

		return $this;
	}

	public function isMultiple(): bool {
		return (bool) $this->getAttribute('multiple', false);
	}

}