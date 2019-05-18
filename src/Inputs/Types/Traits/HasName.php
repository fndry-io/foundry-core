<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasName {

	/**
	 * Name
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return InputType
	 */
	public function setName( string $name ) {
		$this->name = $name;

		return $this;
	}

}