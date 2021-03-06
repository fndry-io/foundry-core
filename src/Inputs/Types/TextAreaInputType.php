<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasMinMax;


/**
 * Class TextAreaInputType
 *
 * @package Foundry\Requests\Types
 */
class TextAreaInputType extends InputType {

	use HasMinMax;

	public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null, string $type = 'text')
    {
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);
        $this->setMultiline(5);
    }

    public function setMultiline( int $number = null ) {
		$this->setAttribute('multiline', $number);
		return $this;
	}

	public function getMultiline() {
		return $this->getAttribute('multiline');
	}

}
