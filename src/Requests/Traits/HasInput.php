<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Inputs\Inputs;

trait HasInput {

	/**
	 * @var Inputs
	 */
	protected $input;

	/**
	 * The input class for this form request
	 *
	 * @return string|null
	 */
	abstract static function getInputClass();

	/**
	 * The rules for this form request
	 *
	 * This is derived off of the input class rules method
	 *
	 * @return array
	 */
	public function rules() {
		return $this->input->rules();
	}

	/**
	 * Make the input class for the request
	 *
	 * @param $inputs
	 *
	 * @return mixed
	 */
	public function makeInput( $inputs ) {
		if ( $class = static::getInputClass() ) {
			return new $class( $inputs );
		} else {
			return null;
		}
	}

	/**
	 * Get the input class for the request
	 *
	 * @return Inputs|null|mixed
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * @param Inputs $input
	 */
	public function setInput( Inputs $input ): void {
		$this->input = $input;
	}
}