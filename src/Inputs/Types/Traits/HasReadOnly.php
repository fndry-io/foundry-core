<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasReadonly {

	public function __construct() {
		$this->setReadonly(false);
	}

	/**
	 * @return bool
	 */
	public function isReadonly(): bool {
		return $this->getAttribute('readonly');
	}

	/**
	 * @param bool $readonly
	 *
	 * @return $this
	 */
	public function setReadonly( bool $readonly = false ) {
		$this->getAttribute('readonly',  $readonly);

		return $this;
	}

}