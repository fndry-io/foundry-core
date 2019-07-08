<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\Referencable;
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
	public function __construct($inputs) {
		$this->types = $this->types();
		$this->fill($inputs);
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
	 * @return array
	 */
	public function rules() {
		return $this->types()->rules();
	}

	/**
	 * Casts the input values to their correct php variable types
	 *
	 * @return mixed
	 */
	protected function cast() {
		foreach (array_keys($this->inputs) as $key) {
			if ($type = $this->getType($key)) {
				if ($type instanceof Castable) {
					$this->inputs[$type->getName()] = $type->getCastValue($this->inputs[$type->getName()]);
				}
				settype($this->inputs[$type->getName()], $type->getCast());
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
		$this->cast();
	}

	public function all()
	{
		return $this->inputs();
	}

	public function keys()
	{
		return $this->types()->keys();
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
		if ($type = $this->getType($name)) {
			if ($type instanceof Castable) {
				$this->inputs[$type->getName()] = $type->getCastValue($this->inputs[$type->getName()]);
			}
			settype($this->inputs[$type->getName()], $type->getCast());
		}
		$this->inputs[$name] = $value;
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
}