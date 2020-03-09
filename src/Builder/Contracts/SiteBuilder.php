<?php

namespace Foundry\Core\Builder\Contracts;

use ArrayAccess;
use Foundry\Core\Contracts\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                'model'
            ];

            foreach ($resources as $key => $resource){

                if(self::array_keys_exists($keys, $resource)){
                    DB::table('foundry_builder_source_types')
                        ->where('name', $key)
                        ->updateOrInsert([
                            'name' => $key,
                            'model' => $resource['model']
                        ]);

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
