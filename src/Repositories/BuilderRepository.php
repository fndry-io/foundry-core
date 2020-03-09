<?php

namespace Foundry\Core\Repositories;

use Foundry\Core\Builder\Contracts\Block;
use Foundry\Core\Builder\Contracts\ResourceRepository;
use Foundry\Core\Models\Site;
use Foundry\Core\Models\Page;
use Foundry\Core\Requests\Response;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Foundry\Builder\Models\Template;

class BuilderRepository
{

    /**
     * Render a block
     *
     * @param $parent | Containing parent for the given block
     * @param $block | The name of the block to be rendered
     * @param $settings | Settings to pass to the block
     * @return Block
     * @throws \Exception
     */
    public function renderBlock($parent, $block, $settings)
    {
        $parents = explode('.', $parent);

        if(sizeof($parents)){
            $resource = $this->findResource($parents);
            try {
                return $this->block($block, $settings, $resource);
            } catch (\Exception $e) {
                throw $e;
            }
        }else
            throw new \Exception("A block can't be rendered outside a container! Please pass the parent container id");

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

            if(!$template)
                throw new \Exception('Template not found!');

            $resource = $this->getTemplateResource($template);

            $id = $parents[0];

            for ($i = 1; $i < sizeof($parents); $i++){
                if($children && sizeof($children)){
                    $id = $id.'.'.$parents[$i];
                    $block = $this->findBlock($children, $id);

                    if(!$block)
                        throw new \Exception("Unable to find block with id $id on template $template->id");

                    $children = $block['children'];

                    if($block['type'] === 'template')
                        $resource = $this->getBlockResource($block['name'], $resource, $block['entity']? $block['entity'] : []);
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
    private function getBlockResource($name, $resource, $settings)
    {
        $block = $this->block($name, $settings, $resource);

        return $block->getResource();
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
     * Get a block
     *
     * @param string $name
     * @param array $settings
     * @param null $resource
     * @return Block
     * @throws \Exception
     */
    public function block($name, $settings = [], $resource = null)
    {
        $class = app('blocks')->get($name);
        if ($class) {
            return new $class($settings, $resource);
        }
        throw new \Exception("Block titled '$name' was not found! Are you sure it is registered?");
    }

    /**
     * Save Builder Site
     *
     * @param array $site
     * @return Response
     */
    public function saveSite(array $site)
    {
        $web_site = Site::query()
            ->where('uuid', $site['id'])->first();

        if (!$web_site)
            $web_site = new Site(['uuid' => $site['id']]);

        $web_site->title = $site['title'];

        if ($web_site->save()) {
            foreach ($site['pages'] as $page) {
                $this->savePage($page, $web_site->id);
            }

            return Response::success();
        }

        return Response::error('Unable to save site, please try again', 500);
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
        $list = $repository->getSelectionList();
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
        return $repository->readResource($id);
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
     * Returns a list of results
     *
     * @param \Closure $builder (QueryBuilder $query) The closure to send the Query Builder to
     * @param int $page
     * @param int $perPage
     *
     * @return Paginator
     */
    public function filter(\Closure $builder = null, int $page = 1, int $perPage = 20): Paginator
    {
        $query = Site::query();

        if ($builder) {
            $query = $builder($query);
        } else {
            $query->select(['*']);
        }

        return $this->paginate($query, $page, $perPage);
    }

    public function browseSites(array $inputs, $page = 1, $perPage = 20): Paginator
    {

        return $this->filter(function (Builder $query) use ($inputs) {
            $query->select('id', 'title', 'uuid')
                ->orderBy('id', 'DESC');

            return $query;
        }, $page, $perPage);
    }

    public function getSite($uuid)
    {
        $site = Site::with('pages')
            ->where('uuid', $uuid)->first()->toArray();


        if ($site) {

            $site['id'] = $site['uuid'];
            unset($site['uuid']);

            $pages = $site['pages'];
            $site['pages'] = array_map(function ($page) {
                $children = $page['children'];
                $page['children'] = array_map(function ($child) {
                    /**
                     * @var $entity Block
                     */
                    $entity = $this->block($child['name']);
                    $child['template'] = $entity->view($child['entity']);

                    return $child;

                }, $children);

                $page['id'] = $page['uuid'];
                unset($page['uuid']);
                return $page;

            }, $pages);

            return Response::success(['site' => $site]);
        }

        return Response::error('Site not found', 404);
    }
}
