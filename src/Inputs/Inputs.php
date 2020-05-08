<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Inputs\Types\Traits\HasValue;
use Foundry\Core\Requests\Contracts\EntityRequestInterface;
use Foundry\Core\Requests\Contracts\ViewableInputInterface;
use Foundry\Core\Support\InputTypeCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
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
     * @var mixed|null The entity if set
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
	 * @param $values
	 */
	public function __construct($values = null, $types = null, $entity = null) {
		if ($types == null) {
			$this->types = $this->types();
		} else {
			$this->types = $types;
		}
		if ($this->types) {
			$this->fillable = array_unique(array_merge($this->fillable, $this->types->names()));
		}
		if ($values) {
			$this->fill($values);
		}
		if ($entity) {
		    $this->setEntity($entity);
        }
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
        $validator = Validator::make($this->values(), $rules, $this->messages());
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
	 * Gets the inputs
	 *
	 * @return array The inputs type cast to their correct values types
	 * @deprecated Use values() method instead
	 */
	public function inputs()
	{
		Log::warning('Inputs::inputs() is deprecated. Please change to calling the values() method.');
		return $this->values();
	}

	/**
	 * Gets the values for this class
	 *
	 * @return array
	 */
	public function values()
	{
		return $this->values;
	}

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


//    /**
//	 * Only extract the desired value
//	 *
//	 * @param array $keys
//	 *
//	 * @return array
//	 */
//	public function only($keys = [])
//	{
//		return Arr::only($this->values, $keys);
//	}

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
		foreach ($this->getTypes() as $type) {
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
	 * @param $values
	 */
	public function fill($values)
	{
		if (!empty($this->fillable)) {
			foreach ($this->fillable as $name) {
				Arr::set($this->values, $name, Arr::get($values, $name));
			}
		} else {
			$this->values = $values;
		}
		$this->cast($this->values);
	}

	public function setValue($key, $value)
    {
        Arr::set($this->values, $key, $value);
        if ($type = $this->getType($key)) {
            $this->castInput($this->values, $type);
        }
    }

	/**
	 * Get all the values
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->values();
	}

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
			return $this->types->get($key);
		} else {
			return false;
		}
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
     * @return FormType|void
     * @throws ValidationException If the request is not to view the form and the validation fails
     */
    public function viewOrValidate(Request $request)
    {
        if ($request->input('_form', false)) {
            if ( $this instanceof ViewableInputInterface ) {
                return $this->view($request);
            } else {
                throw new \Exception( sprintf( 'Input %s must be an instance of ViewableInputInterface to be viewable', get_class( $this ) ) );
            }
        }
        if ($request instanceof EntityRequestInterface && ($entity = $request->getEntity())) {
            $this->setEntity( $entity );
        }
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

}
