<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Inputs;
use Illuminate\Validation\ValidationException;

/**
 * Interface InputInterface
 * @package Foundry\Core\Requests\Contracts
 * @deprecated InputInterface is deprecated in favour of traditional Controllers and Requests
 */
interface InputInterface {

	/**
	 * The rules for this form request
	 *
	 * This is derived off of the input class rules method
	 *
	 * @return array
	 */
	public function rules();

	/**
	 * Make the input class for the request
	 *
	 * @param $inputs
	 *
	 * @return Inputs
	 */
	public function makeInput( $inputs );

	/**
	 * Get the input class for the request
	 *
	 * @return Inputs|null|mixed
	 */
	public function getInput();

	/**
	 * @param Inputs $input
	 */
	public function setInput( Inputs $input ): void;

    /**
     * @return ValidationException|void
     */
    public function validateInputs();
}
