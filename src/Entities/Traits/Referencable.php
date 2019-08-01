<?php

namespace Foundry\Core\Entities\Traits;

use Foundry\Core\Entities\Contracts\HasIdentity;
use Foundry\Core\Entities\Entity;
use LaravelDoctrine\ORM\Facades\EntityManager;

trait Referencable {

	/**
	 * @var string
	 */
	protected $reference_type;

	/**
	 * @var integer
	 */
	protected $reference_id;

	/**
	 * @return int
	 */
	public function getReferenceId(): int {
		return $this->reference_id;
	}

	/**
	 * @return string
	 */
	public function getReferenceType(): string {
		return $this->reference_type;
	}

	/**
	 * @return Entity|object|null
	 */
	public function getReference()
	{
		if ($this->reference_id && $this->reference_type) {
			return EntityManager::getRepository($this->getReferenceType())->find($this->getReferenceId());
		} else {
			return null;
		}
	}

	/**
	 * @param int $reference_id
	 */
	public function setReferenceId( int $reference_id ): void {
		$this->reference_id = $reference_id;
	}

	/**
	 * @param string $reference_type
	 */
	public function setReferenceType( string $reference_type ): void {
		$this->reference_type = $reference_type;
	}

	/**
	 * Attach a reference to the entity
	 *
	 * @param HasIdentity $reference
	 */
	public function attachReference( HasIdentity $reference)
	{
		$this->setReferenceType(get_class($reference));
		$this->setReferenceId($reference->getId());
	}

	/**
	 * Detach the reference
	 */
	public function detachReference() {
		$this->setReferenceId(null);
		$this->setReferenceType(null);
	}

}