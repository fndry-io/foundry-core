<?php

namespace Foundry\Core\Builder;

use Foundry\Core\Builder\Contracts\SiteBuilderResources;
use Illuminate\Support\Arr;

class ResourcesConfig extends SiteBuilderResources
{

    /**
     * All available resources for each site builder page
     *
     * @var array
     */
    protected $resources;


    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return Arr::has( $this->resources, $key );
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ( is_array( $key ) ) {
            return $this->getMany( $key );
        }

        return Arr::get( $this->resources, $key, $default );
    }

    /**
     * Get many blocks
     *
     * @param  array $keys
     *
     * @return array
     */
    public function getMany( $keys ) {

        $resources = [];

        foreach ( $keys as $key => $default ) {
            if ( is_numeric( $key ) ) {
                [ $key, $default ] = [ $default, null ];
            }

            $resources[ $key ] = Arr::get( $this->resources, $key, $default );
        }

        return $resources;
    }

    /**
     * @inheritDoc
     */
    public function items()
    {
        return $this->resources;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value = null)
    {
        $keys = is_array( $key ) ? $key : [ $key => $value ];

        foreach ( $keys as $key => $value ) {
            Arr::set( $this->resources, $key, $value );
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->has( $offset );
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
       return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set( $offset, $value );
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->set( $offset, null );
    }
}
