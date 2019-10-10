<?php

namespace Foundry\Core\Entities\Contracts;


interface HasNode extends HasIdentity
{
    /**
     * @param IsNode $node
     */
    public function setNode($node): void;

    /**
     * @return IsNode|null
     */
    public function getNode();

}
