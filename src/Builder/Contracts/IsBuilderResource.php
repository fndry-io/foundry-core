<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Models\Page;

trait IsBuilderResource{

    /**
     * Allows pre-filling of page data when creating a page for a given resource
     *
     * An array with any/all of the following keys should be returned
     *
     * return [
     *    'name' => '',
     *    'url' => '',
     * //SEO, todo should probably be wrapped in an SEO array
     *    'title' => '',
     *    'keywords' => '',
     *    'description' => ''
     * ]
     * @param string $uri | Resource type URI configuration
     * @return array
     */
    public abstract  function getPageData(string $uri = null): array;


    /**
     * @return array
     */
    public function getPageAttribute()
    {
        $page = [];
        $page['create'] = true;

        $source = array_filter(app()['builder_resources']->items(), function($s){
            return $s['model'] === get_class($this);
        });

        if($source){
            $p = Page::query()
                    ->select(['id'])
                    ->where('resource_id', $this->id)
                    ->where('resource_type', array_keys($source)[0])
                    ->first();

            $page['edit'] = $p? $p->id: null;

        }

        return $page;
    }
}
