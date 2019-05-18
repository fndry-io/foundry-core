<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasRoute {

	protected $route;

	public function setRoute($value)
	{
		$this->route = $value;
		return $this;
	}

	public function getRoute() : ?string
	{
		return $this->route;
	}

}