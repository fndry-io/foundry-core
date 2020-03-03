<?php

namespace Foundry\Core\Builder\Contracts;

use ArrayAccess;
use Foundry\Core\Contracts\Repository;

abstract class SiteBuilder implements Repository, ArrayAccess {

    static function registerBlocks($blocks)
    {
        /** @var Block $block */
        foreach ($blocks as $block){
            app()['blocks']->set($block::getName(), $block);
        }
    }

    static function registerResources($resources)
    {
        foreach ($resources as $key => $resource){
            //todo check if resource has the required keys and is of the correct type
            app()['builder_resources']->set($key, $resource);
        }
    }

    static function getBlocks()
    {
        $blocks = app()['blocks']->items();
        $data = [];
        if ($blocks) {
            /**
             * @var string $name
             * @var Block $class
             */
            foreach ($blocks as $name => $class) {
                array_push($data, [
                    'name' => $name,
                    'label' => $class::getLabel(),
                    'type' => 'template'
                ]);
            }
        }
        return $data;
    }
}
