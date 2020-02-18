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
}
