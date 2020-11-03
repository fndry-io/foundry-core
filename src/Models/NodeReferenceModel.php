<?php

namespace Foundry\Core\Models;

use Foundry\Core\Models\Contracts\HasNode;
use Foundry\Core\Models\Traits\NodeReferenceable;

abstract class NodeReferenceModel extends Model implements HasNode {

	use NodeReferenceable;

}
