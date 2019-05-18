<?php

namespace Foundry\Core\Inputs\Types\Contracts;

interface Choosable {

	public function getOptions(): array;

	public function isExpanded() : bool;

	public function isMultiple() : bool;

}