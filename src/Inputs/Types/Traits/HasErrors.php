<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Contracts\Support\MessageBag as MessageBagContract;
use Illuminate\Support\MessageBag;

trait HasErrors {

	/**
	 * Set the errors
	 *
	 * @param MessageBagContract|array $errors
	 *
	 * @return $this
	 */
	public function setErrors( $errors = [] ) {
		if ( is_array( $errors ) ) {
			$errors = new MessageBag( $errors );
		}
		$this->setAttribute('errors', $errors);

		return $this;
	}

	/**
	 * Get the errors
	 *
	 * @return MessageBagContract
	 */
	public function getErrors(): MessageBagContract {
		return ( $this->attributes['errors'] ) ? $this->getAttribute('errors') : new MessageBag();
	}

	/**
	 * @return bool
	 */
	public function hasErrors() {
		$errors = $this->getErrors();
		return ( $errors && $errors->isNotEmpty() );
	}

}