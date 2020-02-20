<?php

namespace Foundry\Core\Repositories;


use Foundry\Core\Builder\Contracts\Block;
use Foundry\Core\Models\Site;
use Foundry\Core\Models\SitePage;
use Foundry\Core\Requests\Response;

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
}
