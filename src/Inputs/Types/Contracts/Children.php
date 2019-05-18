<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Foundry\Core\Inputs\Types\BaseType;

interface Children {

	/**
	 * Add children to the Type
	 *
	 * @param BaseType ...$types
	 *
	 * @return mixed
	 */
	public function addChildren( BaseType ...$types );

	/**
	 * Get the children for the Type
	 *
	 * @return array
	 */
	public function getChildren(): array;
}