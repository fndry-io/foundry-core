<?php

namespace Foundry\Core\Entities;

use Foundry\Core\Entities\Contracts\HasNode;
use Foundry\Core\Entities\Traits\NodeReferenceable;

abstract class NodeReferenceEntity extends Entity implements HasNode {

	use NodeReferenceable;

}