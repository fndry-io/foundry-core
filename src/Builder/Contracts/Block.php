<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Inputs\Inputs;
use Illuminate\Contracts\Support\Arrayable;

abstract class Block implements Arrayable
{

    /**
     * Location of the view files for this block including the domain
     */
    const TEMPLATE_PATH = '';

    /**
     * @var array Values for the block
     */
    protected $values = [];

    /**
     * Block constructor.
     * @param $values
     */
    public function __construct($values = [])
    {
        $this->init($values);
    }

    /**
     * Init the block with it's values
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     *
     * @param $values
     */
    public function init($values = [])
    {
        $this->setValues($values);
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * Merge provided values with default
     *
     * @return array
     */
    protected function getValues()
    {
        return array_merge($this->getDefaultValues(), $this->values);
    }

    /**
     * Returns an array of all available attributes for a given block
     * "template" attribute is absolutely required to render the view
     * Any variable that is available in the view file needs to be included
     * in this array, unless it is a page resource
     *
     * @return array
     */
    abstract function getDefaultValues(): array;

    /**
     * @return string
     * @throws \Exception
     */
    abstract public function getName(): string;

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTemplate(): string
    {
        if (!static::TEMPLATE_PATH) {
            throw new \Exception('A block needs to provide the path of its templates by overwriting the const field "TEMPLATE_PATH".');
        }
        if (!isset($this->template)) {
            throw new \Exception("No template has been provided for " . static::$name);
        }
        return static::TEMPLATE_PATH . "." . $this->template;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getView(): string
    {
        return (string) view($this->getTemplate(), $this->getValues());
    }

    /**
     * Return the inputs class for controlling the form
     *
     * @return Inputs
     */
    abstract public function getForm(): Inputs;

    /**
     * Converts the block to an array
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        $keys = array_keys(static::getDefaultValues());
        $values = $this->getValues();

        foreach ($keys as $key) {
            $data[$key] = $values[$key];
        }

        return $data;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->values[$name];
        }
        throw new \Exception('Undefined property ' . $name . ' on Block ' . static::$name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->values[$name]);
    }
}


