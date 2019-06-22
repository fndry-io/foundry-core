<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Requests\Response;
use Foundry\Core\Support\InputTypeCollection;
use Illuminate\Contracts\Support\Arrayable;
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
	 * @return Response
	 */
	public function validate() : Response
	{
		$validator = Validator::make($this->inputs(), $this->rules());
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
				settype($this->inputs[$type->getName()], $type->getCast());
			}
		}
	}

	/**
	 * Fill the inputs of this class
	 *
	 * @param $params
	 */
	public function fill($params)
	{
		foreach ($params as $key => $value) {
			if (!isset($this->fillable) || in_array($key, $this->fillable)) {
				$this->inputs[$key] = $value;
			}
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
			settype($value, $type->getCast());
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