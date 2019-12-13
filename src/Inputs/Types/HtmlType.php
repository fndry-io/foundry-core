<?php

namespace Foundry\Core\Inputs\Types;

class HtmlType extends TextInputType
{
    public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        $type = 'html';
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);
    }
}
