<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\BaseType;

trait HasChildren {

	/**
	 * @var BaseType[]
	 */
	protected $children = [];

	/**
	 * Add children to the Type
	 *
	 * @param BaseType ...$types
	 *
	 * @return $this
	 */
	public function addChildren( BaseType ...$types ) {
		foreach ( $types as $type ) {
			$this->children[] = $type;
		}

		return $this;
	}

	/**
	 * Get the children for the Type
	 *
	 * @return array
	 */
	public function getChildren(): array {
		return $this->children;
	}

}