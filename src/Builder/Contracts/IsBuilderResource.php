<?php

namespace Foundry\Core\Builder\Contracts;

interface IsBuilderResource{

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
    public function getPageData(string $uri = null): array;
}
