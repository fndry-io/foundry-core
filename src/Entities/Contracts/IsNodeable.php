<?php

namespace Foundry\System\Entities\Contracts;

use Foundry\Core\Entities\Node;

interface IsNodeable
{
	/**
	 * @return mixed
	 */
	public function getParentNode();

	/**
	 * @param Node $node
	 */
	public function setNode($node): void;

	/**
	 * @return Node|null
	 */
	public function getNode();

	/**
	 * @return Node
	 */
	public function makeNode();

}