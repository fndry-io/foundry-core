<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Inputs\Types\FormType;

abstract class InputCollection extends Inputs {


    abstract public function form() : FormType;

}
