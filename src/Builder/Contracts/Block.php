<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Inputs\Inputs;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use WMDE\VueJsTemplating\Templating;

/**
 * Class Block
 *
 * The base Block class for building Page Builder Blocks
 *
 * @package Foundry\Core\Builder\Contracts
 */
abstract class Block implements Arrayable
{

    /**
     * @var mixed The resource to be given to the block
     */
    protected $resource;

    /**
     * @var array The loaded default settings
     */
    protected $defaults;

    /**
     * @var array The props of the block
     */
    protected $props = [];

    /**
     * @var array The data of the block
     */
    protected $data = [];

    /**
     * Block constructor.
     * @param array $props
     * @param null $resource
     */
    public function __construct($props = [], $resource = null)
    {
        $this->beforeCreated();
        $this->setProps($props);
        $this->setDefaults($this->getDefault());
        $this->setResource($resource);
        $this->created();
    }

    /**
     * Init the block with any additional information
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     */
    public function beforeCreated()
    {

    }

    /**
     * Init the block after creation
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     */
    public function created()
    {

    }

    /**
     * Init the block before render
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     */
    public function beforeRender()
    {

    }

    /**
     * @param array $props
     */
    protected function setProps(array $props)
    {
        $this->props = $props;
    }

    /**
     * Merge provided values with default
     *
     * @return array|string
     */
    public function getProps()
    {
        return array_merge($this->getDefault(), $this->props);
    }

    /**
     * Get a prop value given a key
     *
     * @param $key
     * @return mixed|string
     * @throws \Exception
     */
    public function getProp($key, $default = null)
    {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        } elseif(isset($this->defaults[$key])) {
            return $this->defaults[$key];
        } else {
            return $default;
        }
    }

    /**
     * Merge props with data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
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
     * Returns an array of all available default props for a given block
     *
     * This will be merged into the props given to the block on creation
     *
     * "template" attribute is absolutely required to render the view
     *
     * Any variable that is available in the view file needs to be included
     * in this array, unless it is a resource or a data variable
     *
     * @return array
     */
    abstract public function getDefault(): array;

    /**
     * Get the Block name
     *
     * @return string
     */
    abstract static public function getName(): string;

    /**
     * Get the Block Label
     *
     * @return string
     */
    static public function getLabel(): string
    {
        return Str::title(str_replace('_', ' ' , static::getName()));
    }

    /**
     * Get the template source code for use in rendering
     *
     * @return string
     * @throws \Exception
     */
    protected function getTemplate(): string
    {
        if(is_a($this, IsContainer::class))
            return  '';

        if(static::getType() === 'editor')
            return static::getTemplates();

        if (!sizeof(static::getTemplates())) {
            throw new \Exception('A block needs to provide an array of available templates. At lease one template is required.');
        }

        $template = $this->getProp('template');
        $config = $this->getTemplates()[$template];


        if (empty($template) || !$config || !isset($config['value'])) {
            throw new \Exception("No template has been provided for " . static::getName());
        }

        return  file_get_contents($config['path']);
    }

    /**
     * Returns an array of available templates for the given block
     * return [
     *    'tempate_1' => [
     *          'text' => 'Friendly name',
     *          'value' => 'path to template',
     *          ....
     *      ]
     * ]
     * @return null|array|string
     */
    static abstract function getTemplates();

    /**
     * @param bool $server
     * @param bool $test
     * @return array|string
     * @throws \Exception
     */
    public function render($server = true, $test = false)
    {
        $template =  $this->getTemplate();
        $this->beforeRender();

        $data = [
            'props' => $this->getProps(),
            'block' => array_merge($this->getProps(), $this->getData()),
            'resources' => $this->resource,
        ];

        if($server){
            return  $this->createAndRender($template, $data, [], $test);
        } else {
            $data['template'] = $template;
            return  $data;
        }
    }

    /**
     * @param $template
     * @param array $data
     * @param array $filters
     * @param bool $test
     * @return string
     */
    private function createAndRender($template, array $data, array $filters = [], $test = false ) {
        $templating = new Templating();
        if(!$test) {
            $template = "<template>$template</template>";
        }
        return $templating->render( $template, $data, $filters );
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
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        throw new \Exception('Undefined data property ' . $name . ' on Block ' . static::getName());
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (key_exists($name, $this->props) || key_exists($name, $this->defaults)) {
            throw new \Exception('You cannot override prop ' . $name . ' on Block ' . static::getName() . '. Use a name not already set as a prop or default property.');
        }
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$data[$name]);
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
            'settings' => $this->getProps()
        ];
    }

    public function getStyles(): array
    {
        return  [];
    }

    public function getScripts(): array
    {
        return  [];
    }

    static function getType(): string
    {
        return 'template';
    }

    static function getTemplateOptions()
    {
        return array_values(static::getTemplates());
    }

    public function getClasses(): string
    {
        if(isset($this->props['advanced']) && isset($this->props['advanced']['classes'])){
            return $this->props['advanced']['classes'];
        }
        return  '';
    }

    public function getId(): string
    {
        if(isset($this->props['advanced']) && isset($this->props['advanced']['id'])){
            return $this->props['advanced']['id'];
        }
        return  '';
    }
}


