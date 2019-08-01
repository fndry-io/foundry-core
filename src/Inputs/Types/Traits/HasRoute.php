<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasRoute {

	public function setRoute($value)
	{
		$this->setAttribute('route', $value);

		return $this;
	}

	public function getRoute() : ?string
	{
		return $this->getAttribute('route');
	}

}