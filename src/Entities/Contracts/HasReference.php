<?php

namespace Foundry\Core\Entities\Contracts;

use Foundry\Core\Entities\Entity;

interface HasReference {

    public function reference();

	/**
	 * @return Entity|object|null
	 */
	public function getReference();

	/**
	 * Remove the referenced object
	 *
	 * @return mixed
	 */
	public function detachReference();

	/**
	 * @param HasIdentity $reference
	 */
	public function attachReference( HasIdentity $reference);
}
