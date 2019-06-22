<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Inputs;

interface InputInterface {

	/**
	 * The input class for this form request
	 *
	 * @return string|null
	 */
	static function getInputClass();

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
	 * @return mixed
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
}