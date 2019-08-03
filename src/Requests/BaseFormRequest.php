<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FormRequest
 *
 * @package Foundry\Requests
 */
abstract class BaseFormRequest extends LaravelFormRequest {

	/**
	 * @return bool
	 */
	abstract public function authorize();

	/**
	 * Default rules to apply
	 *
	 * @return array
	 */
	public function rules(){
		return [];
	}

	/**
	 * This is overridden here to support a standard approach to attaching an entity to the request through {_entity}
	 *
	 * @param  \Illuminate\Http\Request  $from
	 * @param  \Illuminate\Http\Request|null  $to
	 *
	 * @return LaravelFormRequest
	 */
	public static function createFrom( \Illuminate\Http\Request $from, $to = null ) {
		$request = parent::createFrom( $from, $to );

		/**
		 * Get the entity associated with the request
		 */
		if (($id = $request->route('_entity')) && ($request instanceof EntityRequestInterface)) {
			$entity = $request->findEntity($id);
			if (!$entity) {
				throw new NotFoundHttpException(__('Item not found'));
			} else {
				$request->setEntity($entity);
			}
		}

		/**
		 * Set the Input
		 */
		if ( $request instanceof InputInterface) {
			$request->setInput( $request->makeInput( $request->all() ) );
		}

		return $request;
	}

	/**
	 * Checks if the request is Authorized
	 *
	 * This uses the first part of validateResolved
	 *
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function validateAuthorization()
	{
		$this->prepareForValidation();

		if (! $this->passesAuthorization()) {
			$this->failedAuthorization();
		}
	}

	/**
	 * Checks if the requested inputs are valid
	 *
	 * This uses the second part of validateResolved
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validateInputs()
	{
		$instance = $this->getValidatorInstance();

		if ($instance->fails()) {
			$this->failedValidation($instance);
		}
	}

}
