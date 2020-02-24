<?php

namespace Foundry\Core\Repositories;


use Foundry\Core\Builder\Contracts\Block;
use Foundry\Core\Builder\Contracts\ResourceRepository;
use Foundry\Core\Models\Site;
use Foundry\Core\Models\SitePage;
use Foundry\Core\Requests\Response;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class BuilderRepository{

    /**
     * Get a block
     *
     * @param $name
     * @return Block
     * @throws \Exception
     */
    public function block($name)
    {
       $class = app('blocks')->get($name);

       if($class){
           return new $class();
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

        if(!$web_site)
            $web_site = new Site(['uuid' => $site['id']]);

        $web_site->title = $site['title'];

        if($web_site->save()){
            foreach ($site['pages'] as $page){
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
        $page = SitePage::query()
                            ->where('uuid', $data['id'])->first();

        if(!$page)
            $page = new SitePage(['uuid' => $data['id']]);

        $page->site_id = $site_id;

        $children = array_map(function($child){
                                    if(isset($child['template']))
                                        unset($child['template']);
                                    return $child;},
                                $data['children']);

        $data['children'] = $children;

        $page->fill($data);

        $page->save();
    }

    public function getResourceList($resource)
    {
        $resource = app()['builder_resources']->get($resource);

        if($resource && count($resource)){
            $repo = $resource['repo'];

            if($repo){
                /**
                 * @var $repository ResourceRepository
                 */
                $repository = new $repo();

                if(is_a($repository, ResourceRepository::class)){
                        $list = $repository->getSelectionList();
                        return Response::success($list);
                }

                return Response::error(sprintf("Resource repo '%s' doesn't implement '%s' contract", get_class($repository), ResourceRepository::class), 406);
            }

            return Response::error("Resource $resource doesn't provide any repo class", 406);
        }

        return Response::error("Builder resource '$resource' was not found", 404);
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


        if($site){

            $site['id'] = $site['uuid'];
            unset($site['uuid']);

            $pages = $site['pages'];
            $site['pages'] = array_map(function($page){
                                $children = $page['children'];
                                $page['children'] = array_map(function($child){
                                    /**
                                     * @var $entity Block
                                     */
                                    $entity = $this->block($child['name']);
                                    $child['template'] = $entity->getView($child['entity']);

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
