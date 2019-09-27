<?php

namespace Foundry\System\Entities\Contracts;

use Foundry\Core\Entities\Contracts\HasIdentity;
use Foundry\Core\Entities\Contracts\HasVisibility;
use Illuminate\Contracts\Support\Arrayable;

interface IsEntity extends HasIdentity, HasVisibility, Arrayable
{
	public function __set($key, $value);

	public function __get($key);

	public function fill($values);

	public function only($keys);

}