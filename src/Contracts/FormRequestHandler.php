<?php

namespace Foundry\Core\Contracts;

use Foundry\Core\Requests\FormRequest;
use Foundry\Core\Requests\Response;

interface FormRequestHandler {

	/**
	 * Register a form request class
	 *
	 * @param FormRequest $class
	 */
	public function register( $class );

	/**
	 * Handle the requested form with the request
	 *
	 * @param $name
	 * @param $request
	 *
	 * @return Response
	 */
	public function handle( $name, $request ): Response;

	/**
	 * Generate the form view object for a requested form and the request
	 *
	 * @param $name
	 * @param $request
	 *
	 * @return Response
	 */
	public function view( $name, $request ): Response;

	/**
	 * The list of registered forms
	 *
	 * @return array
	 */
	public function forms(): array;

}