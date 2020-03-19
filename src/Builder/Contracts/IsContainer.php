<?php

namespace Foundry\Core\Builder\Contracts;

abstract class IsContainer extends Block{

    /**
     * @return array|null|boolean
     */
    static function contains()
    {
        return null;
    }

    /**
     * @return array|null
     */
    static function containers()
    {
        return null;
    }

    /**
     * @return string|null
     */
    static function classes()
    {
        return null;
    }

    /**
     * @return string|null
     */
    static function tag()
    {
        return null;
    }

    static function getContainerAttributes()
    {
        $results = [];

        if(static::containers() !== null){
            $results['containers'] = static::containers();
        }

        if(static::contains() !== null){
            $results['contains'] = static::contains();
        }

        if(static::classes() !== null){
            $results['classes'] = static::classes();
        }

        if(static::tag()){
            $results['tag'] = static::tag();
        }

        return $results;
    }

    protected function getTemplates(): array
    {
        return  [];
    }
}
