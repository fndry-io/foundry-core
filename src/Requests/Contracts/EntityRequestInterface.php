<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Entities\Contracts\EntityInterface;

interface EntityRequestInterface {

	/**
	 * @return EntityInterface|null
	 */
	public function getEntity();

	/**
	 * @param EntityInterface $entity
	 *
	 * @return mixed
	 */
	public function setEntity($entity);

	/**
	 * Find the Entity for the request
	 *
	 * @param $id
	 *
	 * @return EntityInterface|null
	 */
	public function findEntity($id);

}