<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasMinMax;


/**
 * Class TextType
 *
 * @package Foundry\Requests\Types
 */
class TextInputType extends InputType {

	use HasMinMax;

	public function setMultiline( int $number = null ) {
		$this->setAttribute('multiline', $number);
		return $this;
	}

	public function getMultiline() {
		return $this->getAttribute('multiline');
	}

}
