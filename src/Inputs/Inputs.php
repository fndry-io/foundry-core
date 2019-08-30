<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasValue;
use Foundry\Core\Requests\Response;
use Foundry\Core\Support\InputTypeCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

/**
 * Class Inputs
 *
 * Base Inputs class for containing the input key values
 *
 * @package Foundry\Core\Inputs
 */
abstract class Inputs implements Arrayable, \ArrayAccess, \IteratorAggregate {


	/**
	 * @var array Array of rules. These will be merged into the rules from the types
	 */
	public $rules = [];

	/**
	 * @var array The inputs
	 */
	protected $inputs = [];

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
	 * @param $inputs
	 */
	public function __construct($inputs = null, $types = null) {
		if ($types == null) {
			$this->types = $this->types();
		} else {
			$this->types = $types;
		}
		if ($this->types) {
			$this->fillable = array_unique(array_merge($this->fillable, $this->types->names()));
		}
		if ($inputs) {
			$this->fill($inputs);
		}
	}

	/**
	 * Validates the Inputs
	 *
	 * @param array|null $rules
	 * @return Response
	 */
	public function validate($rules = null) : Response
	{
		if (!$rules) {
			$rules = $this->rules();
		}
		$validator = Validator::make($this->inputs(), $rules);
		if ($validator->fails()) {
			return Response::error(__('Error validating request'), 422, $validator->errors());
		}
		return Response::success($validator->validated());
	}

	/**
	 * Gets the inputs
	 *
	 * @return array The inputs type cast to their correct values types
	 */
	public function inputs()
	{
		return $this->inputs;
	}

	/**
	 * Only extract the desired inputs
	 *
	 * @param array $inputs
	 *
	 * @return array
	 */
	public function only($inputs = [])
	{
		return Arr::only($this->inputs, $inputs);
	}

	/**
	 * @param $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function input($key, $default = null)
	{
		return Arr::get($this->inputs, $key, $default);
	}

	/**
	 * Gets the rules for the inputs
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
	 * @param $inputs
	 */
	public function cast(&$inputs) {
		foreach (array_keys($inputs) as $key) {
			if ($type = $this->getType($key)) {
                $this->castInput($inputs, $type);
			}
		}
	}


    /**
     * @param $inputs
     * @param $type
     */
    protected function castInput(&$inputs, $type)
    {
        $name = $type->getName();

        if ($type instanceof Castable) {
            $inputs[$name] = $type->getCastValue($inputs[$name]);
        } else {
            $cast = $type->getCast();
            if ($type instanceof IsMultiple && $type->isMultiple()) {
                if ($inputs[$name]) {
                    $values = [];
                    foreach ($inputs[$name] as $value) {
                        HasValue::castValue($value, $cast);
                        $values[] = $value;
                    }
                    $inputs[$name] = $values;
                }
            } else {
                HasValue::castValue($inputs[$name], $cast);
            }
        }
    }

    /**
	 * Fill the inputs of this class
	 *
	 * @param $inputs
	 */
	public function fill($inputs)
	{
		if (!empty($this->fillable)) {
			foreach ($this->fillable as $name) {
				Arr::set($this->inputs, $name, Arr::get($inputs, $name));
			}
		} else {
			$this->inputs = $inputs;
		}
		$this->cast($this->inputs);
	}

	/**
	 * Get all the inputs
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->inputs();
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
        $this->inputs[$name] = $value;
		if ($type = $this->getType($name)) {
            $this->castInput($this->inputs, $type);
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
		if (isset($this->inputs[$name])) {
			return $this->inputs[$name];
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
	public function getTypes()
	{
		return $this->types;
	}

	public function toArray() {
		return $this->inputs();
	}

	public function offsetExists($offset) {
		return isset($this->inputs[$offset]);
	}

	public function offsetGet($offset){
		return $this->inputs[$offset];
	}

	public function offsetSet($offset, $value){
		$this->$offset = $value;
	}

	public function offsetUnset($offset) {
		unset($this->inputs[$offset]);
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->inputs);
	}

	/**
	 * Create an input class from the given inputs
	 *
	 * @param $inputs
	 *
	 * @return Inputs
	 */
	static function fromInputs($inputs)
	{
		return new static($inputs);
	}

}