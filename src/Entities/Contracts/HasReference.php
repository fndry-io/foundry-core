<?php

namespace Foundry\Core\Entities\Contracts;

use Foundry\Core\Entities\Entity;

interface HasReference {

    public function reference();

	/**
	 * @return int
	 */
	public function getReferenceId(): int;

	/**
	 * @return string
	 */
	public function getReferenceType(): string;

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
