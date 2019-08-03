<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Exceptions\FormRequestException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Foundry\Core\Contracts\FormRequestHandler as FormRequestHandlerInterface;

/**
 * FormRequestHandler
 *
 * This class helps us register and handle form requests
 *
 * Form requests are the basics for doing requests to the Foundry Framework and help us to wrap the system and return
 * standard Foundry Responses for each request
 *
 * @package Foundry\Requests
 */
class FormRequestHandler implements FormRequestHandlerInterface {

	protected $forms;

	/**
	 * Register a form request class
	 *
	 * @param array|string $class The class name
	 *
	 * @return void
	 * @throws FormRequestException
	 */
	public function register( $class ) {

		$class = (array) $class;
		foreach ( $class as $_class ) {
			$this->registerForm( $_class );
		}
	}

	/**
	 * @param $class
	 * @param null $key
	 *
	 * @throws FormRequestException
	 */
	protected function registerForm( $class, $key = null ) {
		if ( $key == null ) {
			$key = forward_static_call( [ $class, 'name' ] );
		}
		if ( isset( $this->forms[ $key ] ) ) {
			throw new FormRequestException( sprintf( "Form key %s already used", $key ) );
		}
		$this->forms[ $key ] = $class;
	}

	/**
	 * Get the form request class
	 *
	 * @param $key
	 * @param Request $request
	 *
	 * @return FormRequest
	 * @throws FormRequestException
	 */
	public function form( $key, $request ): FormRequest {

		/**
		 * @var FormRequest $class
		 */
		$class = $this->getFormRequestClass( $key );

		/**
		 * @var FormRequest $form
		 */
		$form = $class::createFrom($request, new $class);

		return $form;
	}

	/**
	 * Get the form request class
	 *
	 * @param $key
	 *
	 * @return string
	 * @throws FormRequestException
	 */
	protected function getFormRequestClass( $key ): string {
		if ( ! isset( $this->forms[ $key ] ) ) {
			throw new FormRequestException( sprintf( "Form %s not registered", $key ) );
		}

		return $this->forms[ $key ];
	}

	/**
	 * List all the registered forms
	 *
	 * @return array
	 */
	public function forms(): array {
		return array_keys( $this->forms );
	}

	/**
	 * @param $uri
	 * @param $class
	 *
	 * @return Route
	 * @throws FormRequestException
	 */
	public function route($uri, $class) : Route
	{
		$this->register($class);
		return \Illuminate\Support\Facades\Route::match(['get', 'post'],  $uri, '\Foundry\System\Http\Controllers\FormRequestController@resolve')->name($class::name());
	}

}