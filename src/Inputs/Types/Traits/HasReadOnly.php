<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasReadonly {

	/**
	 * Readonly
	 *
	 * @var string $readonly
	 */
	protected $readonly = false;

	/**
	 * @return bool
	 */
	public function isReadonly(): bool {
		return $this->readonly;
	}

	/**
	 * @param bool $readonly
	 *
	 * @return InputType
	 */
	public function setReadonly( bool $readonly = false ) {
		$this->readonly = $readonly;

		return $this;
	}

}