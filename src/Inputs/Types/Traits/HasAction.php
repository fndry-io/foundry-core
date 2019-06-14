<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait HasAction {

	public function getAction() {
		return $this->getAttribute('action');
	}

	public function setAction( string $value = null ) {
		$this->setAttribute('action', $value);

		return $this;
	}

	public function getQuery() {
		return $this->getAttribute('query');
	}

	public function setQuery( array $value = null ) {
		$this->setAttribute('query', $value);

		return $this;
	}

	public function getMethod() {
		return $this->getAttribute('method');
	}

	public function setMethod( string $value = null ) {
		$this->setAttribute('method', $value);

		return $this;
	}

}