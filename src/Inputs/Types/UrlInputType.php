<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasMinMax;

/**
 * Class UrlInputType
 *
 * @package Foundry\Requests\Types
 */
class UrlInputType extends InputType {

	use HasMinMax;

	public function __construct( string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null) {
		$type = 'url';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
		$this->addRule('url');
	}

    public function setValue($value)
    {
        if ($this->inputs) {
            if ($value) {
                $parsedUrl = parse_url($value);

                if (empty($parsedUrl['scheme'])) {
                    $value = 'http://' . $value;
                }
            }

            $this->getInputs()->setValue($this->getName(), $value);
        }
        return $this;
    }
}
