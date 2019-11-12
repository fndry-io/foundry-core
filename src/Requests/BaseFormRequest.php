<?php

namespace Foundry\Core\Requests;

use Closure;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FormRequest
 *
 * The class is responsible for ensuring we inject the entity specified through the route and control the input parameters.
 *
 * We create a Inputs classes to ensure that we get exactly what we want and that the values are correctly cast to the
 * right types
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

	public function setRouteResolver( Closure $callback ) {
		parent::setRouteResolver( $callback );

		/**
		 * Get the entity associated with the request
		 */
        $id = $this->route('_entity', $this->input('_entity', null));
		if ($id && ($this instanceof EntityRequestInterface)) {
			$entity = $this->findEntity($id);
			if (!$entity) {
				throw new NotFoundHttpException(__('Entity not found'));
			} else {
				$this->setEntity($entity);
			}
		}

		/**
		 * Set the Input
		 */
		if ( $this instanceof InputInterface) {
			$this->setInput( $this->makeInput( $this->all() ) );
		}

		return $this;
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
