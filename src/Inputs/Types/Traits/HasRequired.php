<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasRequired {

	/**
	 * Required
	 *
	 * @var string $required
	 */
	protected $required;

	/**
	 * @return bool
	 */
	public function isRequired(): bool {
		return $this->required;
	}

	/**
	 * @param bool $required
	 *
	 * @return InputType
	 */
	public function setRequired( bool $required = true ) {
		$this->required = $required;
		$this->removeRules( 'required', 'nullable' );
		if ( $this->required ) {
			$this->addRule( 'required' );
		} else {
			$this->addRule( 'nullable' );
		}

		return $this;
	}

}