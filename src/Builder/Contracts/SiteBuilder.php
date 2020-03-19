<?php

namespace Foundry\Core\Builder\Contracts;

use ArrayAccess;
use Foundry\Core\Contracts\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Foundry\Builder\Repositories\SourceTypeRepository;

abstract class SiteBuilder implements Repository, ArrayAccess {

    static function registerBlocks($blocks)
    {
        if(!App::runningInConsole()){
            /** @var Block $block */
            foreach ($blocks as $block){
                app()['blocks']->set($block::getName(), $block);
            }
        }

    }

    /**
     * Register various resources of the application
     * Each application resource needs to be of the following format and have the following attributes
     * key => array(
     *   'label' => string|required : Resource display name
     *   'repo'  => string|required: Fully qualified class name of the repository associated with the model of the resource under consideration
     *   'model' => string|required: Fully qualified model name of the resource under consideration
     * )
     * @param $resources
     */
    static function registerResources($resources)
    {
        if(!App::runningInConsole()){
            $keys = [
                'label',
                'repo',
                //'model'
            ];

            $repo = new SourceTypeRepository();

            foreach ($resources as $key => $resource){

                if(self::array_keys_exists($keys, $resource)){
                    $repo->persist(array_merge($resource, ['name' => $key]));
                    app()['builder_resources']->set($key, $resource);
                }else{
                    Log::error(sprintf("The following resource doesn't have all the required keys: %s", json_encode($resource)));
                }
            }
        }

    }

    static function array_keys_exists(array $keys, array $arr) {
        return !array_diff_key(array_flip($keys), $arr);
    }

    static function getBlocks()
    {
        $blocks = app()['blocks']->items();
        $data = [];
        if ($blocks) {
            /**
             * @var string $name
             * @var Block|IsContainer $class
             */
            foreach ($blocks as $name => $class) {

                $attr = [
                        'name' => $name,
                        'label' => $class::getLabel(),
                        'type' => $class::getType()
                    ];

                if(is_a(new $class(), IsContainer::class)){
                    $attr = array_merge($attr, $class::getContainerAttributes());
                }

                array_push($data,$attr);
            }
        }
        return $data;
    }
}
