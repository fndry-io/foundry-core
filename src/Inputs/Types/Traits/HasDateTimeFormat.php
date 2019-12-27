<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasDateTimeFormat {

	/**
	 * @return mixed
	 */
	public function getDateFormat()
	{
		return $this->getAttribute('dateFormat');
	}

	/**
	 * @param $format
	 *
	 * @return $this
	 */
	public function setDateFormat($format)
	{
		$this->setAttribute('dateFormat', $format);
		return $this;
	}


}
