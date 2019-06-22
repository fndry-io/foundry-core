<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

/**
 * Class FormRequest
 *
 * @package Foundry\Requests
 */
abstract class FormRequest extends LaravelFormRequest {

	/**
	 * The name of the Request for registering it in the FormRequest Container
	 *
	 * @return String
	 */
	abstract static function name(): String;

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	abstract public function handle(): Response;

	/**
	 * Authorize the request
	 *
	 * @return Boolean
	 */
	abstract public function authorize();

	/**
	 * Build a form object for this form request
	 *
	 * @return FormType
	 */
	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [
			'_request' => static::name()
		];

		if ($this instanceof EntityRequestInterface) {
			if ($entity = $this->getEntity()) {
				$params['_id'] = $entity->getId();
			}

			$form->setEntity( $this->getEntity() );
		}

		if ( $this instanceof InputInterface) {
			$form->attachInputCollection( $this->getInput()->types() );
			$form->setValues( $this->only( $this->getInput()->keys() ) );
		}
		$form->setAction( route( 'system.request.handle', $params ) );
		$form->setRequest( $this );

		return $form;
	}

}
