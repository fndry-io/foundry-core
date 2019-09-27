<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Model;
use Foundry\Core\Models\Node;

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
		static::created( function ( $model ) {
			/**@var Model|Nodeable $model */
			if (!$model->getNode()) {
				$model->makeNode();
			}
		} );
	}

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
    public function getNode(): ?Node
    {
        return $this->node;
    }

	/**
	 * @return Node|null
	 */
    abstract function getParentNode();

	/**
	 * @return Node
	 */
    public function makeNode(): Node
    {
    	if (!$this->getNode()) {
		    $node = new Node([]);
		    $node->entity()->associate($this);
		    if ($parent = $this->getParentNode()) {
			    $node->setParent($parent->getKey());
		    }
		    $node->save();
	    }
	    return $this->getNode();
    }

}