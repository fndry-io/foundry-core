<?php

namespace Foundry\Core\Inputs\Types;

class MoneyInputType extends NumberInputType {

	protected $symbol = "$";

	public function getSymbol()
	{
		return $this->symbol;
	}

	public function setSymbol($symbol)
	{
		$this->symbol = $symbol;
		return $this;
	}

	public function display( $value = null ) {
		if ($value == null) {
			$value = $this->getValue();
		}
		if ($value) {
			$value = number_format((float) $value, 2);
		} else {
			$value = "--";
		}
		return $this->symbol . "" . $value;
	}

}