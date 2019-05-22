<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Exceptions\FormRequestException;
use Foundry\Core\Requests\Contracts\ViewableFormRequestInterface;

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
class FormRequestHandler implements \Foundry\Core\Contracts\FormRequestHandler {

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
	 * Handle the requested form with the request
	 *
	 * @param $key
	 * @param $request
	 *
	 * @return Response
	 * @throws FormRequestException
	 */
	public function handle( $key, $request ): Response {
		$form = $this->getFormRequest( $key, $request );

		return $form->handle( );
	}

	/**
	 * Generate the form view object for a requested form and the request
	 *
	 * @param $key
	 * @param $request
	 *
	 * @return Response
	 * @throws FormRequestException
	 */
	public function view( $key, $request ): Response {
		$form = $this->getFormRequest( $key, $request );
		if ( $form instanceof ViewableFormRequestInterface ) {
			return Response::success( $form->view() );
		} else {
			throw new FormRequestException( sprintf( 'Requested form %s must be an instance of ViewableFormRequestInterface to be viewable', get_class($form) ) );
		}
	}

	/**
	 * Get the form request class
	 *
	 * @param $key
	 *
	 * @return FormRequest
	 * @throws FormRequestException
	 */
	protected function getFormRequest( $key, $request ): FormRequest {

		/**
		 * @var FormRequest $class
		 */
		$class = $this->getFormRequestClass( $key );
		return $class::createFrom($request);
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

}