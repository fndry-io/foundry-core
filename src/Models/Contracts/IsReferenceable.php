<?php

namespace Foundry\Core\Models\Contracts;

use Foundry\Core\Entities\Entity;

interface IsReferenceable {

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
