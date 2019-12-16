<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasConditions;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class Type
 *
 * @package Foundry\Requests\Types
 */
abstract class BaseType implements Arrayable, \JsonSerializable {

	use HasConditions;

	/**
	 * Type of the input to display
	 *
	 * @var $type
	 */
	protected $type;

	/**
	 * @var string The default cast type for the value of this type
	 */
	protected $cast = 'string';

	/**
	 * @var array
	 */
	protected $attributes = [];

	public function __construct() {
		$class = new \ReflectionClass($this);
		if ($uses = $class->getTraitNames()) {
			foreach ($uses as $trait) {
				if (method_exists($this, '__' . $trait)) {
					call_user_func([$this, $trait]);
				}
			}
		}
		$this->setAttribute('visible', true);
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 *
	 * @return $this
	 */
	public function setType( $type = null ) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get the variable cast type
	 *
	 * @return string
	 */
	public function getCast(): string {
		return $this->cast;
	}

	/**
	 * @param string $cast
	 *
	 * @return $this
	 */
	public function setCast( string $cast ) {
		$this->cast = $cast;
		return $this;
	}

	/**
	 * Get a value from the data property
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->getAttribute('data');
	}

	/**
	 * Set the additional data for the input
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	public function setData(array $data = [])
	{
		$this->setAttribute('data', $data);
		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function appendData($key, $value)
	{
		if (!$this->hasAttribute('data')) {
			$this->setData();
		}
		Arr::set($this->attributes['data'], $key, $value);
		return $this;
	}

	/**
	 * @param string|integer $key
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setAttribute($key, $value)
	{
		Arr::set($this->attributes, $key, $value);
		return $this;
	}

	/**
	 * @param string|integer $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function getAttribute($key, $default = null)
	{
		return Arr::get($this->attributes, $key, $default);
	}

	/**
	 * Append a value to an array attribute
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function appendToAttribute($key, $value)
	{
		if (!$this->hasAttribute($key)) {
			$this->setAttribute($key, []);
		}
		if (!is_array($this->attributes[$key])) {
			throw new \Exception(sprintf('Attribute %s is not an array', $key));
		}
		$this->attributes[$key][] = $value;
		return $this;
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function hasAttribute($key)
	{
		return Arr::exists($this->attributes, $key);
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return $this->jsonSerialize();
	}

	/**
	 * Json serialise field
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {

		$json = array();

		foreach ( $this->attributes as $key => $value ) {

			if ( is_array( $value ) ) {
				$_value = [];
				foreach ( $value as $index => $child ) {
					if ( is_object( $child ) && $child instanceof Arrayable ) {
						$_value[$index] = $child->toArray();
					} else {
						$_value[$index] = $child;
					}
				}
				$value = $_value;
			} elseif ( $value instanceof \Closure) {
				$value = call_user_func($value);
			}
			$json[ $key ] = $value;
		}

		$json['type'] = $this->getType();
		$json['cast'] = $this->getCast();

		return $json;
	}

	/**
	 * Determine if the type is of the one specified
	 *
	 * @param $type
	 *
	 * @return bool
	 */
	public function isType($type)
	{
		return ($this->type === $type);
	}

	/**
	 * @return bool
	 */
	public function isInputType() {
		return $this instanceof Inputable;
	}

	/**
	 * @return bool
	 */
	public function isChoiceType() {
		return $this instanceof Choosable;
	}

	/**
	 * Sets the type as a custom type
	 *
	 * This allows the form renderer to load the type based on a dynamically register type
	 *
	 * This applies when the renderer needs to render the type using another approach or custom element or component
	 *
	 * @param bool $value
     * @return $this
	 */
	public function setCustom(bool $value){
		$this->setAttribute('custom', $value);
        return $this;
	}

    /**
     * Set the cols
     *
     * @param int|array $size The number of columns it should occupy. If you want to set different sizes, pass an array where the keys are "sm", "md", "lg" and the values the number of cols it should occupy.
     * @return $this
     */
    public function setCols($size){
        $this->setAttribute('cols', $size);
        return $this;
    }

}
