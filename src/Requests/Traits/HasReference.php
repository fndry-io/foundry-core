<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\InputInterface;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait HasReference
{

	public function getReference()
	{
		if (($type = $this->input('reference_type')) && ($id = $this->input('reference_id'))) {
			if ($reference = EntityManager::getRepository($type)->find($id)) {
				return $reference;
			} else {
				throw new NotFoundHttpException(__('Associated object not found'));
			}
		} else {
			throw new UnprocessableEntityHttpException(__('Associated object not provided'));
		}
	}

	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [
			'reference_type' => $this->input('reference_type'),
			'reference_id' => $this->input('reference_id')
		];

		if ( $this instanceof InputInterface) {
			$form->attachInputCollection( $this->getInput()->getTypes() );
			$form->setValues( $this->getInput()->getTypes()->values() );
		}

		$form->setAction( route( $this::name(), $params , false) );
		$form->setRequest( $this );

		return $form;
	}

}