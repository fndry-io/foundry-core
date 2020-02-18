<?php

namespace Foundry\Core\Builder\Contracts;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class Block implements Arrayable{

    /**
     * Location of the view files for this block including the domain
     */
    const TEMPLATE_LOCATION = '';

    /**
     * The form, if any for this block in order to edit displayed data
     */
    const FORM = '';


    /**
     * The unique name for this block. No two blocks can have the same name
     */
    static $name = '';

    /**
     * Returns an array of all available attributes for a given block
     * "template" attribute is absolute required to render the view
     * Any variable that is available in the view file needs to be included
     * in this array, unless it is a page resource
     *
     * @return array
     */
    abstract function getDefaultValues(): array;

    /**
     * @return string
     */
    public function getForm(): string
    {
        return static::FORM;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getName() : string
    {
        if(!static::$name){
            throw new \Exception("A block requires a name, please overwrite the NAME constant field");
        }

        return static::$name;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTemplate(): string
    {

        if(!static::TEMPLATE_LOCATION){
            throw new \Exception('A block needs to provide the location of its templates by overwriting the const field "TEMPLATE_LOCATION".');
        }

        if(!isset($this->template)){
            throw new \Exception("No template has been provided for ". static::$name);
        }

        return static::TEMPLATE_LOCATION.".".$this->template;

    }

    /**
     * Merge provided values with default
     *
     * @param $values
     * @return array
     */
    protected function getParams($values)
    {
        return array_merge($this->getDefaultValues(), $values);
    }


    /**
     * @param $values
     * @return string
     * @throws \Exception
     */
    public function getView($values) : string
    {
        $this->fill($this->getParams($values));
        $view = $this->getTemplate();

        return  (string) view($view, $this->toArray());

    }

    /**
     * Converts the block to an array
     *
     * @return array
     */
    public function toArray() {
        $data = [];

        $keys = array_keys(static::getDefaultValues());

        foreach ($keys as $key) {
            $value = $this->__get($key);
            if ($value instanceof Arrayable) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function fill(array $params)
    {
        foreach ($params as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     *
     * This will call any setPropertyName method if it exists
     *
     * @param $name
     * @param $value
     */
    public function __set( $name, $value ) {
        if (method_exists($this, 'set' . Str::ucfirst(Str::camel($name)))) {
            call_user_func([$this, 'set' . Str::ucfirst(Str::camel($name))], $value);
        } else {
            $this->$name = $value;
        }
    }

    /**
     *
     * This will call any getPropertyName method if it exists
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get( $name ) {
        if (method_exists($this, 'get' . Str::ucfirst(Str::camel($name)))) {
            return call_user_func([$this, 'get' . Str::ucfirst(Str::camel($name))]);
        } else {
            return $this->get($name);
        }
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return $this|mixed|null
     */
    public function get($key, $default = null) {
        if (is_null($key) || trim($key) == '') {
            return $this;
        }

        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $count = count($parts);
            for ($i=0;$i<$count;$i++) {
                $end = $count === ($i + 1);
                if (isset($this->{$parts[$i]})) {
                    $item = $this->{$parts[$i]};

                    if ($end) {
                        return $item;
                    }

                    if (empty($item)){
                        return $item;
                    }

                    if ($item instanceof Block) {
                        array_shift($parts);
                        return $item->get(implode('.', $parts), $default);
                    } else {
                        return $item;
                    }
                }
            }
        } elseif (isset($this->{$key})) {
            return $this->{$key};
        }


        return $default;
    }
}


