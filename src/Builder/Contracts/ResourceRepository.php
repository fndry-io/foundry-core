<?php

namespace Foundry\Core\Builder\Contracts;

interface ResourceRepository{
    /**
     * Fetch a list of resource objects based off of resource type
     * @return object
     */
    public function getSelectionList(): object;

    /**
     * Fetch the Object resource
     *
     * @param int $id
     * @return object
     */
    public function readResource(int $id): object;
}
