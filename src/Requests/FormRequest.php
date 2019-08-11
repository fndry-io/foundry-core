<?php

namespace Foundry\Core\Requests;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\FormRequestInterface;
use Foundry\Core\Requests\Contracts\InputInterface;

/**
 * Class FormRequest
 *
 * @package Foundry\Requests
 */
abstract class FormRequest extends BaseFormRequest implements FormRequestInterface {

	/**
	 * Build a form object for this form request
	 *
	 * If an entity is supplied through the route {_entity} it will be set into the form here
	 *
	 * All inputs from the request are also passed into the form using the the keys (names) of the inputs in the Input Class
	 *
	 * @return FormType
	 */
	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [];

		if ($this instanceof EntityRequestInterface && ($entity = $this->getEntity())) {

			$params['_entity'] = $entity->getId();
			$form->setEntity( $entity );
		}

		if ( $this instanceof InputInterface) {
			$form->attachInputCollection( $this->getInput()->getTypes() );

			$form->setValues( $this->only( $this->getInput()->keys() ) );
		}


		$form->setAction( route( $this::name(), $params , false) );
		$form->setRequest( $this );

		return $form;
	}


}
