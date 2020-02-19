<?php

namespace Foundry\Core\Builder\Contracts;

use ArrayAccess;
use Foundry\Core\Contracts\Repository;

abstract class SiteBuilder implements Repository, ArrayAccess {

    static function registerBlocks($blocks)
    {
        foreach ($blocks as $block){
            app()['blocks']->set($block::$name, $block);
        }
    }

    static function registerPageResources($resources)
    {
        foreach ($resources as $key => $resource){
            //todo check if resource is a type of read request
            app()['page_resources']->set($key, $resource);
        }
    }
}
