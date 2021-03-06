<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasPlaceholder {

	/**
	 * Placeholder
	 *
	 * @var string $placeholder
	 */
	protected $placeholder;

	/**
	 * @return string|null
	 */
	public function getPlaceholder() {
		return $this->getAttribute('placeholder');
	}

	/**
	 * @param string $placeholder
	 *
	 * @return $this
	 */
	public function setPlaceholder( string $placeholder = null ) {
		$this->setAttribute('placeholder', $placeholder);

		return $this;
	}

}
