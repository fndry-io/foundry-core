<?php

namespace Foundry\Core\Builder;

use Foundry\Core\Builder\Contracts\SiteBuilder;
use Illuminate\Support\Arr;

class BlocksConfig extends SiteBuilder
{

    /**
     * All available blocks for the application
     *
     * @var array
     */
    protected $blocks;


    public function __construct(array $blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return Arr::has( $this->blocks, $key );
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ( is_array( $key ) ) {
            return $this->getMany( $key );
        }

        return Arr::get( $this->blocks, $key, $default );
    }

    /**
     * Get many blocks
     *
     * @param  array $keys
     *
     * @return array
     */
    public function getMany( $keys ) {

        $blocks = [];

        foreach ( $keys as $key => $default ) {
            if ( is_numeric( $key ) ) {
                [ $key, $default ] = [ $default, null ];
            }

            $blocks[ $key ] = Arr::get( $this->blocks, $key, $default );
        }

        return $blocks;
    }

    /**
     * @inheritDoc
     */
    public function items()
    {
        return $this->blocks;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value = null)
    {
        $keys = is_array( $key ) ? $key : [ $key => $value ];

        foreach ( $keys as $key => $value ) {
            Arr::set( $this->blocks, $key, $value );
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
