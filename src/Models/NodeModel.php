<?php

namespace Foundry\Core\Models;

use Foundry\Core\Models\Contracts\HasNode;
use Foundry\Core\Models\Contracts\IsNodeable;
use Foundry\Core\Models\Traits\Nodeable;

/**
 * Class NodeModel
 *
 * A Node allows as to build a hierachial structure of the central content in the system
 *
 * Doing this allows us to achieve both a hierachy structure as well as a morphable quality in the system
 *
 * A Node represents the hierachial structure of content in the system
 *
 * Content then linked to a Node only needs to store the node of the entity, allowing it to easily link one or
 * many content types to that Node. E.G. Company has many Comments
 *
 * Having the Hiearchial structure (as a Node is a Treeable trait) allows us also to retrieve all associated content
 * of a type within any desired tree level
 *
 * @package Foundry\Core\Models
 */
abstract class NodeModel extends Model implements IsNodeable, HasNode {

	use Nodeable;

}
