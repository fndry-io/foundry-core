<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasRequired {

	/**
	 * @return bool
	 */
	public function isRequired(): bool {
		return (bool) $this->getAttribute('required');
	}

	/**
	 * @param bool $required
	 *
	 * @return $this
	 */
	public function setRequired( bool $required = true ) {
		$this->setAttribute('required', $required);
		$this->removeRules( 'required', 'nullable' );
		if ( $this->isRequired() ) {
			$this->addRule( 'required' );
		} else {
			$this->addRule( 'nullable' );
		}

		return $this;
	}

}