<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Response;

interface FormRequestInterface {

	/**
	 * The name of the Request for registering it in the FormRequest Container
	 *
	 * @return String
	 */
	static function name(): string;

	/**
	 * Build a form object for this form request
	 *
	 * @return FormType
	 */
	public function form(): FormType;

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	public function handle(): Response;

}