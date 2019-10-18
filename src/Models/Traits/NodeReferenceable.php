<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Node;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait NodeReferenceable
 *
 * Allows entities to reference an existing Node in the system
 *
 * This is used on entities which would normally contain a morphable property
 *
 * Using this trait allows the system to better control referential integrity but also maintain a morphable quality
 *
 * @package Foundry\System\Models\Traits
 */
trait NodeReferenceable
{

    /**
     * @return BelongsTo
     */
	public function node()
	{
		return $this->belongsTo(Node::class)->withoutGlobalScopes();
	}

	/**
	 * @param Node $node
	 */
    public function setNode($node): void
    {
        $this->node()->associate($node);
    }

	/**
	 * @return Node|null
	 */
    public function getNode(): ?Node
    {
        return $this->node;
    }

}
