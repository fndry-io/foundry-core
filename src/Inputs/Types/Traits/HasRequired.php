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
	 * @param bool $rule
	 *
	 * @return $this
	 */
	public function setRequired( bool $required = true, bool $rule = true ) {
		$this->setAttribute('required', $required);
		$this->removeRules( 'required', 'nullable' );
		if ($rule === true) {
			if ( $required ) {
				$this->addRule( 'required' );
			} else {
				$this->addRule( 'nullable' );
			}
		}

		return $this;
	}

}
