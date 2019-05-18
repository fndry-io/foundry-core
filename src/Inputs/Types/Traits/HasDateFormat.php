<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasDateFormat {

	public function getFormat()
	{
		return $this->format;
	}

	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}

}