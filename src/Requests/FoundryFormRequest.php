<?php

namespace Foundry\Core\Requests;

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
abstract class FoundryFormRequest extends LaravelFormRequest {

    /**
     * @var bool Controls if a NotFoundHttpException should be thrown if the entity is not found
     */
    protected $throwNotFoundException = true;

    /**
     * @var string The default entity key to use to pull the entity from the route
     */
    protected $entityRouteKey = '_entity';

    /**
     * @var null The entity id if there is one in the request
     */
    protected $entityId = null;

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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        parent::prepareForValidation();

        /**
         * Get the entity associated with the request
         */
        $this->entityId = $this->route($this->entityRouteKey, $this->input($this->entityRouteKey, null));
        if ($this instanceof EntityRequestInterface) {
            $entity = $this->findEntity($this->entityId);
            if (!$entity && $this->throwNotFoundException) {
                throw new NotFoundHttpException(__('Record not found'));
            } else {
                $this->setEntity($entity);
            }
        }

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
