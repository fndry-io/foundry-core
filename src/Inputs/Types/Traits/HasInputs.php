<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Inputs;

trait HasInputs
{
	/**
	 * @var Inputs|null
	 */
	protected $inputs;

	public function getInputs() : ?Inputs
	{
		return $this->inputs;
	}

	public function setInputs(Inputs &$inputs)
	{
		$this->inputs = $inputs;
	}

	public function getEntity()
	{
		return $this->inputs->getEntity();
	}

	public function hasEntity()
	{
		return $this->inputs->hasEntity();
	}
}
