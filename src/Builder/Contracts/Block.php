<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Inputs\Inputs;

abstract class Block
{

    /**
     * Location of the view files for this block including the domain
     */
    const TEMPLATE_PATH = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array Values for the block
     */
    protected $values = [];

    /**
     * @var array Default values for the block
     */
    protected $defaults = [];

    /**
     * Block constructor.
     * @param $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->defaults = $this->getDefaultValues();
        $this->init($data);
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
     * Get the value from the block values or defaults
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function getValue($key, $default = null)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } elseif (isset($this->defaults[$key])) {
            return $this->defaults[$key];
        } else {
            return $default;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_merge($this->getDefaultValues(), $this->data);
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
    abstract static public function getName(): string;

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTemplate(): string
    {
        if (!static::TEMPLATE_PATH) {
            throw new \Exception('A block needs to provide the path of its templates by overwriting the const field "TEMPLATE_PATH".');
        }
        $template = $this->getValue('template');
        if (empty($template)) {
            throw new \Exception("No template has been provided for " . static::getName());
        }
        return static::TEMPLATE_PATH . "." . $template;
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
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }
        throw new \Exception('Undefined property ' . $name . ' on Block ' . static::getName());
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


