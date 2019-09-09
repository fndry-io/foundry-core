<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait HasParams {

	public function setParams($params)
	{
		$this->setAttribute('params', $params);
		return $this;
	}

	public function getParams()
	{
		return $this->getAttribute('params');
	}

}