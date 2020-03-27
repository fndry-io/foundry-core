<?php

namespace Foundry\Core\Repositories;

use Carbon\Carbon;
use Foundry\Core\Builder\Contracts\Block;
use Foundry\Core\Builder\Contracts\ResourceRepository;
use Foundry\Core\Models\Page;
use Foundry\Core\Requests\Response;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\Foundry\Builder\Entities\ResourceManager;
use Modules\Foundry\Builder\Models\Template;
use Modules\Foundry\Builder\Repositories\TemplateRepository;

class BuilderRepository
{

    /**
     * Render a block
     *
     * @param $parent | Containing parent for the given block
     * @param $block | The name of the block to be rendered
     * @param $props | Props to pass to the block
     * @param ResourceManager $resource | Parent resource if available
     * @return Block
     * @throws \Exception
     */
    public function renderBlock($parent, $block, $props, ResourceManager $resource = null)
    {
        $parents = explode('.', $parent);

        if(sizeof($parents)){
            if(!$resource)
                $resource = $this->findResource($parents);
            try {
                return $this->block($block, $props, $resource);
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \Exception("A block can't be rendered outside a container! Please pass the parent container id");
        }


    }

    /**
     * @param array $parents
     * @return mixed|object|null
     * @throws \Exception
     */
    private function findResource(array $parents)
    {
        if(sizeof($parents)){
            /**
             * @var $template Template
             */
            $template = Template::query()
                                    ->where('id', $parents[0])
                                    ->first();

            $children = $template->children;

            if(!$template) {
                throw new \Exception('Template not found!');
            }


            $resource = new ResourceManager(null,$this->getTemplateResource($template));

            $id = $parents[0];

            for ($i = 1; $i < sizeof($parents); $i++){
                if($children && sizeof($children)){
                    $id = $id.'.'.$parents[$i];
                    $block = $this->findBlock($children, $id);

                    if(!$block) {
                        throw new \Exception("Unable to find block with id $id on template $template->id");
                    }

                    $children = $block['children'];

                    /**
                     * Own resource should now become parent resource for this block
                     */
                    if($resource->getOwnResource()){
                        $resource->setParentResource($resource->getOwnResource());
                        $resource->setOwnResource(null);
                    }

                    //if($block['type'] === 'template') {
                        $resource = $this->getBlockResource($block['name'], $resource, $block['data'] && isset($block['data']['block'])? $block['data']['block'] : []);
                    //}

                }
            }

            return $resource;
        }

        return null;
    }


    /**
     * @param array $array
     * @param $id
     * @return mixed|null
     */
    private function findBlock(array $array, $id)
    {
        foreach ($array as $child){
            if($child['id'] === $id)
                return $child;
        }

        return null;
    }

    /**
     * Get the resource for a given block
     *
     * @param $name | block name
     * @param $resource | parent block resource
     * @param $settings | settings for block
     * @return mixed
     * @throws \Exception
     */
    private function getBlockResource($name, ResourceManager $resource, $settings)
    {
        $block = $this->block($name, $settings, $resource);

        return $block->getResourceManager();
    }

    /**
     * Determines if a template has a fixed resource
     * and fetches it if required
     *
     * @param Template $template
     * @return object|null
     */
    public function getTemplateResource(Template $template)
    {
        if($template->resource_type && $template->resource_id){
            return $this->getResource($template->resource_type, (int) $template->resource_id);
        }

        return null;
    }

    /**
     * Determines if a page has a fixed resource
     * and fetches it if required
     *
     * @param Page $page
     * @return object|null
     */
    public function getPageResource(Page $page)
    {
        if($page->resource_type && $page->resource_id){
            return $this->getResource($page->resource_type, (int) $page->resource_id);
        }

        return null;
    }

    /**
     * Get a block
     *
     * @param string $name
     * @param array $props
     * @param ResourceManager $resource
     * @return Block
     * @throws \Exception
     */
    public function block($name, $props = [], ResourceManager $resource = null)
    {
        $class = app('blocks')->get($name);
        if ($class) {
            return new $class($props, $resource);
        }
        throw new \Exception("Block titled '$name' was not found! Are you sure it is registered?");
    }

    /**
     * Save a builder site page
     *
     * @param array $data
     * @param int $site_id
     */
    private function savePage(array $data, int $site_id)
    {
        $page = Page::query()
            ->where('uuid', $data['id'])->first();

        if (!$page)
            $page = new Page(['uuid' => $data['id']]);

        $page->site_id = $site_id;

        $children = array_map(function ($child) {
            if (isset($child['template']))
                unset($child['template']);
            return $child;
        },
            $data['children']);

        $data['children'] = $children;

        $page->fill($data);

        $page->save();
    }

    /**
     * Find resource repo class
     *
     * @param $resource
     * @return ResourceRepository|Response
     */
    public function getResourceRepo($resource)
    {
        $resource = app()['builder_resources']->get($resource);

        if ($resource && count($resource)) {
            $repo = $resource['repo'];

            if ($repo) {
                /**
                 * @var $repository ResourceRepository
                 */
                $repository = new $repo();

                if (is_a($repository, ResourceRepository::class)) {
                   return $repository;
                }

                return Response::error(sprintf("Resource repo '%s' doesn't implement '%s' contract", get_class($repository), ResourceRepository::class), 406);
            }

            return Response::error("Resource $resource doesn't provide any repo class", 406);
        }

        return Response::error("Builder resource '$resource' was not found", 404);
    }

    /**
     * Get array of objects for a given resource name
     *
     * @param $resource
     * @return Response
     */
    public function getResourceList($resource)
    {
        $repository = $this->getResourceRepo($resource);
        $list = $repository->getResourceSelectionList();
        return Response::success($list);
    }

    /**
     * Get a resource object
     *
     * @param $resource | resource type
     * @param $id | id of the fixed resource
     *
     * @return object
     */
    public function getResource($resource, $id)
    {
        $repository = $this->getResourceRepo($resource);
        return $repository->read($id);
    }

    /**
     * @param $query
     * @param $page
     * @param $perPage
     * @param $pageName
     *
     * @return Paginator
     */
    protected function paginate(Builder $query, $page, $perPage, $pageName = 'page'): Paginator
    {
        return $query->paginate($perPage, null, $pageName, $page);
    }

    /**
     * Fetch a page from the DB based off of the url
     *
     * @param $url
     * @param string $status
     * @return array
     * @throws \Exception
     */
    public function renderPage($url, $status = 'published'){

        $query = Page::query()
                        ->where('url', $url)
                        ->where('status', $status);

        if($status === 'published'){
            $query->where('published_at', '<=', Carbon::now());
        }

        /**
         * @var $page Page
         */
        $page = $query->first();


        if(!$page)
            abort(404,'Page not found!');

        $blocks = [];

        /**
         * @var $layout Template
         * @var $content Template
         */
        $layout = TemplateRepository::repository()->read($page->layout_id);
        $content = TemplateRepository::repository()->read($page->content_layout_id);

        $resource = $this->getPageResource($page);

        if(!$layout || !$content){
            //todo show appropriate error message
        }else{
            $blocks = $this->renderBlocks($layout, $content, $resource);
        }

        return [$page, $blocks];
    }

    /**
     * Create a blocks tree
     *
     * @param Template $template
     * @param Template $content
     * @param $page_resource
     * @return array
     */
    private function renderBlocks(Template $template, Template $content, $page_resource)
    {
        $children = $template->children;
        $parent_resource = $this->getTemplateResource($template);

        /**
         * @var $resource ResourceManager
         */
        $resource = new ResourceManager(null,$parent_resource,$page_resource);

        $tree = function($parent, &$block, ResourceManager $resource) use (&$tree, $content) {

            $data = Arr::get($block, 'props', []);

            if($block['type'] === 'content'){
                $contentResource = $this->getTemplateResource($content);
                if($contentResource) {
                    $resource->setParentResource($contentResource);
                }

                $block['children'] = $content->children;
            }

            $block['block'] = $this->renderBlock($parent, $block['name'], $data, $resource);

            if(sizeof($block['children'])){
                foreach ($block['children'] as &$child){
                    $tree($block['id'], $child, new ResourceManager(null, $block['block']->getResource(),$resource->getPageResource()));
                }

            }

        };

        foreach ($children as &$child){
            $tree($template->id, $child, $resource);
        }

        return $children;
    }

}
