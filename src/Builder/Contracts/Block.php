<?php

namespace Foundry\Core\Builder\Contracts;

use Foundry\Core\Inputs\Inputs;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use WMDE\VueJsTemplating\Templating;

abstract class Block implements Arrayable
{

    /**
     * Location of the view files for this block including the domain
     */
    const TEMPLATE_PATH = '';

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
    protected $data = [];

    /**
     * Block constructor.
     * @param array $data
     * @param null $resource
     */
    public function __construct($data = [], $resource = null)
    {
        $this->beforeCreate();
        $this->setData($data);
        $this->setDefaults($this->getDefault());
        $this->setResource($resource);
        $this->created();
    }

    /**
     * Init the block with any additional information
     *
     * This should be overridden in extending classes if the values need to be expanded on, or additional data fetched
     */
    public function beforeCreate()
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
     * @param array $data
     */
    protected function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Merge provided values with default
     *
     * @return array
     */
    public function getData()
    {
        return array_merge($this->getDefault(), $this->data);
    }

    /**
     * Get the value from the block values or defaults
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function get($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
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
    abstract public function getDefault(): array;

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
     * @param bool $server
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

        $template = $this->get('template');
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
        return view($this->getTemplate(), ['settings' => $this->getData(), 'data' => $this->data, 'resource' => $this->resource]);
    }

    /**
     * @param bool $server
     * @param bool $test
     * @return array|string
     * @throws \Exception
     */
    public function render($server = true, $test = false)
    {
        $this->beforeRender();

        $template =  $this->getTemplate();

        $data = [
            'block' => $this->getData(),
            'resources' => $this->resource,
        ];

        if($server){
            return  $this->createAndRender($template, $data, [], $test);
        }else{

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
        if(!$test)
            $template = "<template>$template</template>";
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
            'settings' => $this->getData()
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
}


