<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasPosition {

	/**
	 * Position
	 *
	 * @var string $position
	 */
	protected $position;

	/**
	 * @return string
	 */
	public function getPosition(): string {
		return $this->position;
	}

	/**
	 * @param string $position
	 *
	 * @return InputType
	 */
	public function setPosition( string $position = null ) {
		$this->position = $position;

		return $this;
	}

}