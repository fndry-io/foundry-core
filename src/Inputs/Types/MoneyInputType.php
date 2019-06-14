<?php

namespace Foundry\Core\Inputs\Types;

class MoneyInputType extends NumberInputType {

	public function MoneyInputType()
	{
		$this->setAttribute('symbol', '$');
	}

	public function getSymbol()
	{
		return $this->getAttribute('symbol');
	}

	public function setSymbol($symbol)
	{
		$this->setAttribute('symbol', $symbol);
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
		return $this->getAttribute('symbol') . "" . $value;
	}

}