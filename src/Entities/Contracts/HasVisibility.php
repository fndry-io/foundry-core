<?php

namespace Foundry\Core\Entities\Contracts;

interface HasVisibility
{

	public function isVisible($key): bool;

	public function makeVisible($key);

	public function isHidden($key): bool;

	public function makeHidden($key);

}