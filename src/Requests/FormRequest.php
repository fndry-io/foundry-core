<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\FormRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class FormRequest
 *
 * @package Foundry\Requests
 */
abstract class FormRequest extends LaravelFormRequest implements FormRequestInterface {


	/**
	 * The name of the Request for registering it in the FormRequest Container
	 *
	 * @return String
	 */
	abstract static function name(): String;

	/**
	 * @return bool
	 */
	abstract public function authorize();

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	abstract public function handle(): Response;

	/**
	 * Build a form object for this form request
	 *
	 * @return FormType
	 */
	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [];

		if ($this instanceof EntityRequestInterface) {
			if ($entity = $this->getEntity()) {
				$params['_entity'] = $entity->getId();
			}

			$form->setEntity( $this->getEntity() );
		}

		if ( $this instanceof InputInterface) {
			$form->attachInputCollection( $this->getInput()->types() );
			$form->setValues( $this->only( $this->getInput()->keys() ) );
		}
		$form->setAction( route( $this::name(), $params , false) );
		$form->setRequest( $this );

		return $form;
	}


}
