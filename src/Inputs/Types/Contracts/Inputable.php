<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Foundry\System\Entities\Entity;

interface Inputable {

	public function getName(): string;

	public function setName(string $name);

	public function setEntity( Entity &$entity = null );

	/**
	 * @return null|Entity
	 */
	public function getEntity();

	public function hasEntity(): bool;

	public function display($value = null);

}