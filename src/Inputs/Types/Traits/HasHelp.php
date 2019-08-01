<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasHelp {

	/**
	 * Help
	 *
	 * @var string $help
	 */
	protected $help;

	/**
	 * @return string
	 */
	public function getHelp() {
		return $this->getAttribute('help');
	}

	/**
	 * @param string $help
	 *
	 * @return $this
	 */
	public function setHelp( string $help = null ) {
		$this->setAttribute('help', $help);

		return $this;
	}

}