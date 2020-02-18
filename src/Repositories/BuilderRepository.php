<?php

namespace Foundry\Core\Repositories;


use Foundry\Core\Builder\Contracts\Block;

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

       throw new \Exception("Block title $name was not found! Are you sure it is registered?");
    }

}
