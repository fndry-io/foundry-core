<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Model;
use Foundry\Core\Models\Node;
use Foundry\System\Entities\Contracts\IsNode;

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
	 * @param IsNode $node
	 */
    public function setNode($node): void
    {
        $this->node = $node;
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