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
abstract class FormRequest extends FoundryFormRequest implements FormRequestInterface {

	/**
	 * Build a form object for this form request
	 *
	 * If an entity is supplied through the route {_entity} it will be set into the form here
	 *
	 * All inputs from the request are also passed into the form using the the keys (names) of the inputs in the Input Class
	 *
	 * @return FormType
	 */
	public function form($params = []): FormType {

		$form   = new FormType( static::name() );

		if ($this instanceof EntityRequestInterface && ($entity = $this->getEntity())) {
			$params['_entity'] = $entity->getKey();
			$form->setEntity( $entity );
		}

		if ( $this instanceof InputInterface) {
			$form->attachInputCollection( $this->getInput()->getTypes() );

			$inputs = $this->only($this->getInput()->keys());
			$this->getInput()->cast($inputs);

			$form->setValues( $inputs );
		}

		$form->setAction( resourceUri( $this::name()) );
		$form->setParams($params);
		$form->setRequest( $this );

		return $form;
	}


}
