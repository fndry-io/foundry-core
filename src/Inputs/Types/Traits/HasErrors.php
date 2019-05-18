<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Contracts\Support\MessageBag as MessageBagContract;
use Illuminate\Support\MessageBag;

trait HasErrors {

	/**
	 * Errors
	 *
	 * @var MessageBag $errors
	 */
	protected $errors;

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
		$this->errors = $errors;

		return $this;
	}

	/**
	 * Get the errors
	 *
	 * @return MessageBagContract
	 */
	public function getErrors(): MessageBagContract {
		return ( $this->errors ) ? $this->errors : new MessageBag();
	}


	public function hasErrors() {
		return ( $this->errors && $this->errors->isNotEmpty() );
	}

}