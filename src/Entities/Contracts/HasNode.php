<?php

namespace Foundry\Core\Entities\Contracts;

use Foundry\Core\Entities\Node;

interface HasNode extends HasIdentity
{
    /**
     * @param Node $node
     */
    public function setNode($node): void;

    /**
     * @return Node|null
     */
    public function getNode();

}
