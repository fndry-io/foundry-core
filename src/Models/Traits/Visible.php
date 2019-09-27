<?php

namespace Foundry\Core\Models\Traits;

trait Visible
{

	public function isVisible($key): bool
	{
		return in_array($key, $this->getVisible()) || $this->getVisible() == ['*'];
	}

	public function isHidden($key): bool
	{
		return in_array($key, $this->getHidden()) || $this->getHidden() == ['*'];
	}

}