<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait HasAction {

	protected $action;

	protected $method;

	protected $query;

	public function getAction() {
		return $this->action;
	}

	public function setAction( string $value = null ) {
		$this->action = $value;

		return $this;
	}

	public function getQuery() {
		return $this->query;
	}

	public function setQuery( array $value = null ) {
		$this->query = $value;

		return $this;
	}

	public function getMethod() {
		return $this->method;
	}

	public function setMethod( string $value = null ) {
		$this->method = $value;

		return $this;
	}

}