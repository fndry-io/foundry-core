<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasTimeFormat {

	/**
	 * @return mixed
	 */
	public function getTimeFormat()
	{
		return $this->getAttribute('timeFormat');
	}

	/**
	 * @param $format
	 *
	 * @return $this
	 */
	public function setTimeFormat($format)
	{
		$this->setAttribute('timeFormat', $format);
		return $this;
	}

}