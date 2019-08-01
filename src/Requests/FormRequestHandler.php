<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Exceptions\FormRequestException;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;
use Foundry\Core\Requests\Contracts\ViewableFormRequestInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

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
	 * @param $id
	 *
	 * @return Response
	 * @throws FormRequestException
	 */
	public function handle( $key, $request, $id = null ): Response {
		$form = $this->getFormRequest( $key, $request );

		if ($form instanceof EntityRequestInterface) {
			$entity = $form->findEntity($id);
			if (!$entity) {
				return Response::error(__('Item not found'), 404);
			} else {
				$form->setEntity($entity);
			}
		}

		if (!$form->authorize()) {
			return Response::error(__('Unauthorized'), 403);
		}

		if ($form instanceof InputInterface) {
			$response = $form->getInput()->validate($form->rules());
			if (!$response->isSuccess()) {
				return $response;
			}
		}

		return $form->handle( );
	}

	/**
	 * Generate the form view object for a requested form and the request
	 *
	 * @param $key
	 * @param $request
	 * @param $id
	 *
	 * @return Response
	 * @throws FormRequestException
	 */
	public function view( $key, $request, $id = null ): Response {
		$form = $this->getFormRequest( $key, $request );

		if ($form instanceof EntityRequestInterface) {
			$entity = $form->findEntity($id);
			if (!$entity) {
				return Response::error(__('Item not found'), 404);
			} else {
				$form->setEntity($entity);
			}
		}

		if (!$form->authorize()) {
			return Response::error(__('Unauthorized'), 403);
		}

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
	 * @param Request $request
	 *
	 * @return FormRequest
	 * @throws FormRequestException
	 */
	protected function getFormRequest( $key, $request ): FormRequest {

		/**
		 * @var FormRequest $class
		 */
		$class = $this->getFormRequestClass( $key );

		/**
		 * @var FormRequest $form
		 */
		$form = $request::createFrom($request, new $class);


		if ( $form instanceof EntityRequestInterface && ($id = $form->input( '_id' )) ) {
			if ($entity = $form->findEntity( $id )) {
				$form->setEntity($entity);
			}
		}

		if ( $form instanceof InputInterface) {
			$form->setInput( $form->makeInput( $form->all() ) );
		}

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
		return \Illuminate\Support\Facades\Route::match(['get', 'post'],  $uri, '\Foundry\System\Http\Controllers\FormRequestController@handle')->name($class::name());
	}

}