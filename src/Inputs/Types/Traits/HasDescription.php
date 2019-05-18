<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasDescription {

	/**
	 * Description
	 *
	 * @var string $description
	 */
	protected $description;

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return InputType
	 */
	public function setDescription( string $description = null ) {
		$this->description = $description;

		return $this;
	}

}