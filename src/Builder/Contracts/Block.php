<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Inputs\Inputs;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

abstract class Block implements Arrayable
{

    /**
     * Location of the view files for this block including the domain
     */
    const TEMPLATE_PATH = '';

    /**
     * @var array The blocks data after init. This would be the data the block is responsible for getting.
     */
    protected $data;

    /**
     * @var mixed The resource to be given to the block
     */
    protected $resource;

    /**
     * @var array The loaded default settings
     */
    protected $defaults;

    /**
     * @var array Values for the block
     */
    protected $settings = [];

    /**
     * Block constructor.
     * @param array $settings
     * @param null $resource
     */
    public function __construct($settings = [], $resource = null)
    {
        $this->setSettings($settings);
        $this->setDefaults($this->getDefaultSettings());
        $this->setResource($resource);
        $this->init();
    }

    /**
     * Init the block with any additional information
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     */
    public function init()
    {

    }

    /**
     * @param array $settings
     */
    protected function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Merge provided values with default
     *
     * @return array
     */
    public function getSettings()
    {
        return array_merge($this->getDefaultSettings(), $this->settings);
    }

    /**
     * Get the value from the block values or defaults
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function getSetting($key, $default = null)
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        } elseif (isset($this->defaults[$key])) {
            return $this->defaults[$key];
        } else {
            return $default;
        }
    }

    /**
     * @return array
     */
    protected function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    protected function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    /**
     * @param mixed $resource
     */
    public function setResource($resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns an array of all available attributes for a given block
     * "template" attribute is absolutely required to render the view
     * Any variable that is available in the view file needs to be included
     * in this array, unless it is a page resource
     *
     * @return array
     */
    abstract public function getDefaultSettings(): array;

    /**
     * @return string
     */
    abstract static public function getName(): string;

    /**
     * @return string
     */
    static public function getLabel(): string
    {
        return Str::title(str_replace('_', ' ' , static::getName()));
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTemplate(): string
    {
        if (!static::TEMPLATE_PATH) {
            throw new \Exception('A block needs to provide the path of its templates by overwriting the const field "TEMPLATE_PATH".');
        }
        $template = $this->getSetting('template');
        if (empty($template)) {
            throw new \Exception("No template has been provided for " . static::getName());
        }
        return static::TEMPLATE_PATH . "." . $template;
    }

    /**
     * Generate a View for being rendered
     *
     * This method should be overridden by implementing classes when they need to fetch and set extra data to the view file
     *
     * @return View
     * @throws \Exception
     */
    public function getView(): View
    {
        //todo handle the exception properly to comply with Laravel View and not throwing exceptions
        return view($this->getTemplate(), ['settings' => $this->getSettings(), 'data' => $this->data, 'resource' => $this->resource]);
    }

    /**
     * Return the inputs class for controlling the form and the settings for the block
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
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        throw new \Exception('Undefined data property ' . $name . ' on Block ' . static::getName());
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        return [
            'name' => static::getName(),
            'template' => $this->view()->render(),
            'settings' => $this->getSettings()
        ];
    }

}


