<?php

namespace Foundry\Core\Inputs\Types\Contracts;

interface Choosable {

	/**
	 * @return null|array
	 */
	public function getOptions();

	public function isExpanded() : bool;

	public function isMultiple() : bool;

}