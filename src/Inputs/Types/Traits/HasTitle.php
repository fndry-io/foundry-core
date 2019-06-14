<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasTitle {

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->getAttribute('title');
	}

	/**
	 * @param string $title
	 *
	 * @return $this
	 */
	public function setTitle( string $title = null ) {
		$this->setAttribute('title', $title);

		return $this;
	}

}