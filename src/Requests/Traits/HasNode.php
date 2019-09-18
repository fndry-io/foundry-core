<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Requests\Contracts\InputInterface;
use Foundry\Core\Entities\Node;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait HasNode
{

	/**
	 * @return Node
	 */
	public function getNode()
	{
		if ($id = $this->input('node')) {
			/**
			 * @var Node $node
			 */
			if ($node = EntityManager::getRepository(Node::class)->find($id)) {
				return $node;
			} else {
				throw new NotFoundHttpException(__('Associated object not found'));
			}
		} else {
			throw new UnprocessableEntityHttpException(__('Associated object not provided'));
		}
	}

	/**
	 * @return FormType
	 */
	public function form(): FormType {

		$form   = new FormType( static::name() );
		$params = [
			'node' => $this->input('node')
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