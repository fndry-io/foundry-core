<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;

trait HasTitle {

	/**
	 * Title
	 *
	 * @var string $title
	 */
	protected $title;

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return InputType
	 */
	public function setTitle( string $title = null ) {
		$this->title = $title;

		return $this;
	}

}