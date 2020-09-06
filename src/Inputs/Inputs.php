<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Entities\Contracts\HasVisibility;
use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Inputs\Types\Traits\HasValue;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\ViewableInputInterface;
use Foundry\Core\Support\InputTypeCollection;
use Foundry\System\Events\BeforeInputValidate;
use Foundry\System\Events\FormCreated;
use Foundry\System\Events\InputCreated;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class Inputs
 *
 * Base Inputs class for containing the input key values
 *
 * @package Foundry\Core\Inputs
 */
abstract class Inputs implements Arrayable, \ArrayAccess, \IteratorAggregate {

    /**
     * @var mixed|null|Model The entity if set
     */
    protected $entity = null;

	/**
	 * @var array Array of rules. These will be merged into the rules from the types
	 */
	public $rules = [];

	/**
	 * @var array The input value array
	 */
	protected $values = [];

	/**
	 * @var InputTypeCollection The collection of input types
	 */
	protected $types;

	/**
	 * @var array The array of fillable input names
	 */
	protected $fillable = [];

	/**
	 * Inputs constructor.
	 *
     * @param null|array $values
     * @param null|InputTypeCollection $types
     * @param null|Model|array $entity
     */
	public function __construct($values = null, $types = null, $entity = null) {
        if ($entity) {
            $this->setEntity($entity);
        }
		if ($types == null) {
			$this->setTypes($this->types());
		} else {
            $this->setTypes($types);
		}
		if ($this->types) {
			$this->fillable = array_unique(array_merge($this->fillable, $this->types->names()));
		}
        if ($values) {
            $this->fill($values);
        }
        event(new InputCreated($this));
	}

    /**
     * Sets the types and links them up with this inputs class
     *
     * @param InputTypeCollection $types
     * @return $this
     */
	public function setTypes(InputTypeCollection $types)
    {
        $this->types = $types;
        /** @var InputType $type */
        foreach($this->types as &$type) {
            $type->setInputs($this);
        }
        return $this;
    }

	/**
	 * Validates the Inputs
	 *
	 * @param array|null $rules
     * @return true
     * @throws ValidationException
     */
	public function validate($rules = null)
	{
		if (!$rules) {
			$rules = $this->rules();
		}
        $validator = Validator::make($this->values, $rules, $this->messages());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return true;
	}

	public function messages()
    {
        return [];
    }

	/**
	 * Gets the values that were set into the input
	 *
	 * @return array
	 */
	public function values()
	{
        return $this->values;
	}

    /**
     * Merges the entity values with the values of the inputs
     *
     * @return array
     */
    public function all()
    {
        $values = [];
        /** @var InputType $input */
        foreach($this->types as $input) {
            if ($input->isVisible() && !$input->isHidden()) {
                $value = $input->getValue();
                if ($value === null) {
                    $value = $input->getDefault();
                }
                Arr::set($values, $input->getName(), $value );
            }
        }
        return array_merge($this->values, $values);
    }

    /**
     * Get only the inputs for the given keys
     *
     * @param $keys
     * @return array
     */
    public function only($keys)
    {
        $results = [];
        $placeholder = null;
        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($this->values, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }
        return $results;
    }

	/**
     * Get a value
     *
	 * @param $key
	 * @param null $default
	 *
	 * @return mixed
     * @deprecated Use value() instead
	 */
	public function input($key, $default = null)
	{
		return Arr::get($this->values, $key, $default);
	}

    /**
     * Get the specific value from the input
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function value($key, $default = null)
    {
        return Arr::get($this->values, $key, $default);
    }

    /**
	 * Gets the rules from the input types
	 *
	 * This will also merge the input rules into the final produce list of rules
	 *
	 * @return array
	 */
	public function rules() {
		$rules = $this->types()->rules();
		if ($this->rules) {
			foreach ($this->rules as $key => $rule) {
				if ($rules[$key]) {
					if (!is_array($rules[$key])) {
						$rules[$key] = array(
							$rules[$key]
						);
					}
					array_push($rules[$key], $rule);
				} else {
					$rules[$key] = $rule;
				}
			}
		}
		return $rules;
	}

	/**
	 * @param $key
	 * @param $rule
	 */
	public function addRule($key, $rule){
		$this->rules[$key] = $rule;
	}

	/**
	 * Casts the input values to their correct php variable types
	 *
	 * @param $values
	 */
	public function cast(&$values) {
		foreach ($this->types as $type) {
            $this->castInput($values, $type);
		}
	}


    /**
     * @param $values
     * @param $type
     */
    protected function castInput(&$values, $type)
    {
        $name = $type->getName();
        $value = Arr::get($values, $name);

        if ($value === null) {
        	return;
        }

        if ($type instanceof Castable) {
        	$value = $type->getCastValue($value);
        } else {
            $cast = $type->getCast();
            if ($type instanceof IsMultiple && $type->isMultiple()) {
                    $_values = [];
                    foreach ($value as $_value) {
                        HasValue::castValue($_value, $cast);
                        $_values[] = $_value;
                    }
	                $value = $_values;
            } else {
                HasValue::castValue($value, $cast);
            }
        }
	    Arr::set($values, $name, $value);
    }

    /**
	 * Fill the values of this class
     *
     * We fill the values through the types
     *
     * Each type will ultimately call the setValue on this class, which will then cast the value correctly
	 *
	 * @param $values
	 */
	public function fill($values)
	{
	    $keys = !empty($this->fillable) ? $this->fillable : array_keys($values);
        foreach ($keys as $key) {
            /** @var InputType $type */
            $type = $this->getType($key);
            $value = Arr::get($values, $key);
            if ($type && $value !== null) {
                $type->setValue($value);
            }
        }
	}

    /**
     * Set a value in the inputs
     *
     * @param $key
     * @param $value
     */
	public function setValue($key, $value)
    {
        Arr::set($this->values, $key, $value);
        if ($type = $this->getType($key)) {
            $this->castInput($this->values, $type);
        }
    }

    /**
     * Alias for fill()
     *
     * @param array $values
     */
    public function setValues($values = [])
    {
        $this->fill($values);
    }

    /**
     * Get's a list of all the keys for the input types
     *
     * @return array
     */
	public function keys()
	{
		return $this->types()->keys()->toArray();
	}

	/**
	 * Determine if an input is fillable
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function isFillable($name)
	{
		if (isset($this->fillable)) {
			if ($this->fillable === true || in_array($name, $this->fillable)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Sets an input
	 *
	 * @param $name
	 * @param $value
	 */
	public function __set( $name, $value ) {
        $this->values[$name] = $value;
		if ($type = $this->getType($name)) {
            $this->castInput($this->values, $type);
        }
	}

	/**
	 * Gets an input value
	 *
	 * @param $name
	 *
	 * @return mixed|null The type cast value of the input
	 */
	public function __get( $name ) {
		if (isset($this->values[$name])) {
			return $this->values[$name];
		} else {
			return null;
		}
	}

	/**
	 * The types to associate with the input
	 *
	 * @return InputTypeCollection
	 */
	abstract function types() : InputTypeCollection;

	/**
	 * Get the type for the given input key
	 *
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function getType($key)
	{
		if ($this->types->has($key)) {
			return $this->types[$key];
		} else {
			return false;
		}
	}

    /**
     * Checks if the inputs have an input type of the given name
     *
     * @param $key
     * @return bool
     */
	public function hasType($key)
    {
        return $this->types->has($key);
    }

	/**
	 * @return InputTypeCollection|null
	 */
	public function getTypes($keys = null)
	{
	    if ($keys) {
	        return $this->types()->only($keys);
        } else {
            return $this->types;
        }
	}

	public function toArray() {
		return $this->values();
	}

	public function offsetExists($offset) {
		return isset($this->values[$offset]);
	}

	public function offsetGet($offset){
		return $this->values[$offset];
	}

	public function offsetSet($offset, $value){
		$this->$offset = $value;
	}

	public function offsetUnset($offset) {
		unset($this->values[$offset]);
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->values);
	}

	/**
	 * Create an input class from the given values
     *
     * @param array $values
     * @param null $types
     * @param null $entity
     * @return static
     */
	static function make(array $values, $types = null, $entity = null)
	{
		return new static($values, $types, $entity);
	}

    /**
     * @param Request $request
     * @param boolean $with_entity If the entity in the request should be loaded into the inputs
     * @return FormType|void
     * @throws ValidationException If the request is not to view the form and the validation fails
     */
    public function viewOrValidate(Request $request, $with_entity = true)
    {
        /**
         * todo This should be removed from here as it is better to control the setting of the entity through the
         *  calling code rather than assuming it here. It was found that a request could be for an entity, but the form
         *  is for a child of that entity or related to that entity, but the input isn't. This would result in the form
         *  being filled with values not related to the input itself.
         */
        if ($with_entity && $request instanceof EntityRequestInterface && ($entity = $request->getEntity())) {
            $this->setEntity( $entity );
        }
        if ($request->input('_form', false)) {
            if ( $this instanceof ViewableInputInterface ) {
                $form = $this->view($request);
                event(new FormCreated($form));
                return $form;
            } else {
                throw new \Exception( sprintf( 'Input %s must be an instance of ViewableInputInterface to be viewable', get_class( $this ) ) );
            }
        }
        event(new BeforeInputValidate($this));
        $this->validate();
    }

    /**
     * Set the entity for the inputs
     *
     * @param mixed $entity
     *
     * @return $this
     */
    public function setEntity( $entity = null ) {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * Determines if the input has an entity
     *
     * @return bool
     */
    public function hasEntity(){
        return $this->entity !== null;
    }

    /**
     * Get the value for a given key on the entity / values
     *
     * It will first find the entity value if set and then look at the inputs->values for any override of that vaue
     *
     * @param string $key The key name of the input type
     *
     * @return mixed|null
     */
    public function getValue( $key ) {
        $value = null;
        if ( $this->entity ) {
            $value = obj_arr_get($this->entity, $key);
        }
        $_value = Arr::get($this->values, $key);
        if ($_value !== null) {
            $value = $_value;
        }
        return $value;
    }

    /**
     * Get the fields visible state off the entity
     *
     * @param $key
     *
     * @return bool
     */
    public function isVisible($key)
    {
        $entity = $this->getEntity();
        if ($entity && $entity instanceof HasVisibility) {
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $key = array_pop($parts);
                foreach($parts as $part){
                    if ($entity->{$part} && $entity->{$part} instanceof HasVisibility) {
                        $entity = $entity->{$part};
                    } else {
                        return true;
                    }
                }
            }
            return $entity->isVisible($key);
        }
        return true;
    }

    /**
     * Get the fields hidden state off the entity
     *
     * @param $key
     *
     * @return bool
     */
    public function isHidden($key)
    {
        $entity = $this->getEntity();
        if ($entity && $entity instanceof HasVisibility) {
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $key = array_pop($parts);
                foreach($parts as $part){
                    if ($entity->{$part} && $entity->{$part} instanceof HasVisibility) {
                        $entity = $entity->{$part};
                    } else {
                        return false;
                    }
                }
            }
            return $entity->isHidden($key);
        }
        return false;
    }



}
