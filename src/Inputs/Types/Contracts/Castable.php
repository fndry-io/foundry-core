<?php

namespace Foundry\Core\Inputs\Types\Contracts;

interface Castable {

	public function getCastValue($value);
}