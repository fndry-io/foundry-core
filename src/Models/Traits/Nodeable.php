<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Node;
use Foundry\Core\Entities\Contracts\HasNode;
use Foundry\Core\Entities\Contracts\IsNode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait Nodeable
 *
 * Allows an entity to be a Node in the system to which content can be connected or associated
 *
 * @package Foundry\System\Models\Traits
 */
trait Nodeable
{

	/**
	 * Boot the Nodeable
	 */
	protected static function bootNodeable() {
		static::created( function ( HasNode $model ) {
            $model->makeNode();
		} );
	}

    /**
     * @return BelongsTo
     */
	public function node()
	{
		return $this->belongsTo(Node::class);
	}

	/**
	 * @param IsNode $node
	 */
    public function setNode($node): void
    {
        $this->node()->associate($node);
    }

	/**
	 * @return IsNode|null
	 */
    public function getNode(): ?IsNode
    {
        return $this->node;
    }

	/**
	 * @return IsNode|null
	 */
    abstract function getParentNode();

	/**
	 * @return IsNode
	 */
    public function makeNode(): IsNode
    {
    	if (!$this->node) {
		    $node = new Node([]);
		    $node->setEntity($this);
		    if ($parent = $this->getParentNode()) {
			    $node->parent()->associate($parent);
		    } else {
		    	$node->makeRoot();
		    }
		    $node->save();
		    $this->node()->associate($node);
		    $this->save();
	    }
    	return $this->node;
    }

}
