<?php
/**
 * Created by PhpStorm.
 * User: greg
 * Date: 14/Jan/2019
 * Time: 2:16 PM
 */

namespace Foundry\Core\Inputs\Types\Traits;


trait HasMultiple {

	/**
	 * @var bool $multiple Used to determine if we allow multiple selections (collections, chechbox vs radio, or select vs select[multiple]
	 */
	protected $multiple = false;

	/**
	 * @return bool
	 */
	public function getMultiple(): bool {
		return $this->multiple;
	}

	/**
	 * @param int $multiple
	 *
	 * @return $this
	 */
	public function setMultiple( bool $multiple = null ) {
		$this->multiple = $multiple;

		return $this;
	}

	public function isMultiple(): bool {
		return $this->multiple;
	}

}