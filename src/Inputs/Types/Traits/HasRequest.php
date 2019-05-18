<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasRequest {

	protected $request_url;
	protected $request_name;
	protected $request_id = null;


	public function setRequestUrl( string $url = null ) {
		$this->request_url = $url;

		return $this;
	}

	public function setRequestName( string $name = null ) {
		$this->request_name = $name;

		return $this;
	}

	public function setRequestId( $id = null ) {
		$this->request_id = $id;

		return $this;
	}

	public function getRequestUrl(): string {
		return $this->request_url;
	}

	public function getRequestName(): string {
		return $this->request_name;
	}

	public function getRequestId() {
		return $this->request_id;
	}

}