<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Entities\Entity;
use Foundry\Core\Models\Model;

interface EntityRequestInterface {

	/**
	 * @return Entity|Model|null
	 */
	public function getEntity();

	/**
	 * @param Entity|Model $entity
	 *
	 * @return mixed
	 */
	public function setEntity($entity);

	/**
	 * Find the Entity for the request
	 *
	 * @param $id
	 *
	 * @return Entity|Model|null
	 */
	public function findEntity($id);

}