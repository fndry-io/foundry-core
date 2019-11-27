<?php

namespace Foundry\Core\Inputs\Types;

class JsonType extends TextInputType
{
    protected $cast = 'array';

    public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        $type = 'json';
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);
        $this->setMultiline(10);
    }
}
