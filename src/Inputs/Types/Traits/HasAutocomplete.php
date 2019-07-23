<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasAutocomplete {

	public function setAutocomplete($state = true){
		$this->setAttribute('autocomplete', ($state) ? 'on' : 'off');
		return $this;
	}

	public function getAutocomplete()
	{
		return $this->getAttribute('autocomplete');
	}
}