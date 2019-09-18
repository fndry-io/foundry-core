<?php

namespace Foundry\Core\Entities\Traits;

use Foundry\Core\Entities\Node;
use LaravelDoctrine\ORM\Facades\EntityManager;

/**
 * Trait Nodeable
 *
 * Allows an entity to be a Node in the system to which content can be connected or associated
 *
 * @package Foundry\System\Entities\Traits
 */
trait Nodeable
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
    	if (!$this->node) {
    		$this->node = $this->makeNode();
	    }
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
    	$node = new Node([]);
    	$node->attachEntity($this);

    	if ($parent = $this->getParentNode()) {
    		$node->setParent($parent);
	    }

    	EntityManager::persist($node);
	    EntityManager::flush($node);
	    return $node;
    }

}