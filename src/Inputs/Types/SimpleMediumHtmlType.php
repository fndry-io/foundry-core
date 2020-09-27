<?php

namespace Foundry\Core\Inputs\Types;

class SimpleMediumHtmlType extends MediumHtmlType
{
    public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder);
        $this->setSimpleMode();
    }
}
