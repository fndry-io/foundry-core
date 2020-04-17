<?php

namespace Foundry\Core\Entities\Contracts;

interface HasVisibility
{

	public function isVisible($key): bool;

	public function makeVisible($key);

	//this is already handled by Guardable trait from Laravel, but for some reason defining it here breaks things
	//public function isHidden($key): bool;

	public function makeHidden($key);

}
