<?php

namespace Foundry\Core\Inputs\Types;

class MoneyInputType extends NumberInputType {


	public function __construct( string $name, string $label = null, bool $required = true, string $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null ) {
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder );
		$this->setSymbol('$');
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