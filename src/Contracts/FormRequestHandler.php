<?php

namespace Foundry\Core\Contracts;

use Foundry\Core\Requests\Contracts\ViewableFormRequestInterface;
use Foundry\Core\Requests\FormRequest;
use Foundry\Core\Requests\Response;
use Illuminate\Routing\Route;

interface FormRequestHandler {

	/**
	 * Register a form request class
	 *
	 * @param FormRequest $class
	 */
	public function register( $class );

	/**
	 * Generate the form and return it
	 *
	 * @param $name
	 * @param $request
	 *
	 * @return FormRequest
	 */
	public function form( $name, $request ): FormRequest;

	/**
	 * The list of registered forms
	 *
	 * @return array
	 */
	public function forms(): array;

	/**
	 * @param $uri
	 * @param $class
	 *
	 * @return Route
	 */
	public function route($uri, $class) : Route;

}