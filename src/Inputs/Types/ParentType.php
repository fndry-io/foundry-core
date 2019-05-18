<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Children;
use Foundry\Core\Inputs\Types\Traits\HasChildren;

abstract class ParentType extends BaseType implements Children {

	use HasChildren;
}