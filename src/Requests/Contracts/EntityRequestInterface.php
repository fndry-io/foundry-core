<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Entities\Entity;

interface EntityRequestInterface {

	/**
	 * @return Entity|null
	 */
	public function getEntity();

	/**
	 * @param Entity $entity
	 *
	 * @return mixed
	 */
	public function setEntity($entity);

	/**
	 * Find the Entity for the request
	 *
	 * @param $id
	 *
	 * @return Entity|null
	 */
	public function findEntity($id);

}