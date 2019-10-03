<?php

namespace Foundry\Core\Entities\Contracts;

interface IsNodeable
{
	/**
	 * @return mixed
	 */
	public function getParentNode();

	/**
	 * @param IsNode $node
	 */
	public function setNode($node): void;

	/**
	 * @return IsNode|null
	 */
	public function getNode();

}
