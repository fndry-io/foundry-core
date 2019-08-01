<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Inputs\Inputs;

trait HasInput {

	/**
	 * @var Inputs
	 */
	protected $input;

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
	abstract public function makeInput( $inputs );

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

	public function validate()
	{
		return $this->input->validate();
	}

	public function messages() {
		return [];
	}

	public function attributes() {
		return [];
	}
}