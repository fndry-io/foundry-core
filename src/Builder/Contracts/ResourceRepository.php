<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Models\Model;

interface ResourceRepository{
    /**
     * Fetch a list of resource objects based off of resource type
     * Must return an array with the following keys
     * return [
     *    'text' => '',
     *    'value' => ''
     * ]
     * @return object
     */
    public function getResourceSelectionList(): object;

    /**
     * Fetch the Object resource
     *
     * @param $entity
     * @return Model
     */
    public function read($entity): Model;
}
