<?php
/**
 * Created by PhpStorm.
 * User: greg
 * Date: 13/Jan/2019
 * Time: 1:20 PM
 */

namespace Foundry\Core\Inputs\Types\Traits;


trait HasClass {

	protected $class;

	public function setClass( $class = null ) {
		$this->class = $class;

		return $this;
	}

	public function getClass() {
		return $this->class;
	}
}