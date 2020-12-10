<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Entities\Entity;
use Foundry\Core\Models\Model;

/**
 * Interface EntityRequestInterface
 * @package Foundry\Core\Requests\Contracts
 * @deprecated This contract is no longer used. Use ModelRequestInterface instead
 */
interface EntityRequestInterface {

	/**
	 * @return Entity|Model|null
     * @deprecated This contract is no longer used. Use ModelRequestInterface instead
	 */
	public function getEntity();

	/**
	 * @param Entity|Model $entity
	 *
	 * @return mixed
     * @deprecated This contract is no longer used. Use ModelRequestInterface instead
	 */
	public function setEntity($entity);

	/**
	 * Find the Entity for the request
	 *
	 * @param $id
	 *
	 * @return Entity|Model|null
     * @deprecated This contract is no longer used. Use ModelRequestInterface instead
	 */
	public function findEntity($id);

}
