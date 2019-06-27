<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasDateFormat {

	/**
	 *
	 */
	public function __HasDateFormat()
	{
		if (!isset($this->format)) {
			$this->setAttribute('format', $this->format);
		} else {
			$this->setAttribute('format', "Y-m-d H:i:s");
		}
	}

	/**
	 * @return mixed
	 */
	public function getFormat()
	{
		return $this->getAttribute('format');
	}

	/**
	 * @param $format
	 *
	 * @return $this
	 */
	public function setFormat($format)
	{
		$this->setAttribute('format', $format);
		return $this;
	}

}