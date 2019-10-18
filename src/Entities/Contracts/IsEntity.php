<?php

namespace Foundry\Core\Entities\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface IsEntity extends HasIdentity, HasVisibility, Arrayable
{
	public function __set($key, $value);

	public function __get($key);

	public function fill(array $values);

	public function only($keys);

}
