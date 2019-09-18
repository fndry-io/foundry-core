<?php

namespace Foundry\Core\Entities\Traits;

use Foundry\Core\Entities\Node;

/**
 * Trait NodeReferenceable
 *
 * Allows entities to reference an existing Node in the system
 *
 * This is used on entities which would normally contain a morphable property
 *
 * Using this trait allows the system to better control referential integrity but also maintain a morphable quality
 *
 * @package Foundry\System\Entities\Traits
 */
trait NodeReferenceable
{
    /**
     * @var Node
     */
    protected $node;

	/**
	 * @param Node $node
	 */
    public function setNode($node): void
    {
        $this->node = $node;
    }

	/**
	 * @return Node|null
	 */
    public function getNode()
    {
        return $this->node;
    }

}