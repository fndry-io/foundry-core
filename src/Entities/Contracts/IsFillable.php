<?php

namespace Foundry\Core\Entities\Contracts;

interface IsFillable
{
	public function fill(array $values);
}