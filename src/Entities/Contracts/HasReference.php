<?php

namespace Foundry\Core\Entities\Contracts;

use Foundry\Core\Entities\Entity;

interface HasReference {

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
	 * @param int $reference_id
	 */
	public function setReferenceId( int $reference_id ): void;

	/**
	 * @param string $reference_type
	 */
	public function setReferenceType( string $reference_type ): void;
}