<?php

namespace Foundry\Core\Support;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Entities\Entity;
use Illuminate\Support\Collection;

/**
 * InputTypeCollection
 *
 * This class represents a collection of input types and offers convenience methods for extracting information from the
 * inputs it contains
 *
 * @package Foundry\Core\Support
 */
class InputTypeCollection extends Collection {

	/**
	 * Create an InputTypeCollection from a list of input types
	 *
	 * @param $types
	 *
	 * @return InputTypeCollection
	 */
	static public function fromTypes($types)
	{
		$collection = new static();
		foreach ($types as $type) {

			if ($type instanceof FormType) {
				$parent = $type->getName();
				foreach (static::fromTypes($type->getInputs()) as $child) {
					$key = "{$parent}.{$child->getName()}";
					/**
					 * @var Inputable $child
					 */
					$collection->put($key, $child->setName($key));
				}
			} else {
				/**
				 * @var Inputable $type
				 */
				$collection->put($type->getName(), $type);
			}
		}
		return $collection;
	}

	/**
	 * Get all the rules of the inputs
	 *
	 * @return array
	 */
	public function rules() {
		$rules = [];
		foreach ( $this->keys() as $key ) {

			/**
			 * @var InputType $item
			 */
			$item = $this->get($key);

			if ($item instanceof InputTypeCollection) {
				foreach ($item->rules() as $name => $rule) {
					$rules["$key.$name"] = $rule;
				}
			} elseif ($item instanceof Choosable && $item->isMultiple()) {
				if ($item->isRequired()) {
					$rules[ $item->getName() ] = 'array';
				}
				$rules[ $item->getName() . '.*' ] = $item->getRules();
			} else {
				$rules[ $item->getName() ] = $item->getRules();
			}
		}
		return $rules;
	}

	/**
	 * Gets all the names of the inputs
	 *
	 * This can be used to filter an array, such as the inputs coming in from a request
	 *
	 * @param null $root
	 *
	 * @return array
	 */
	public function names($root = null)
	{
		$names = [];
		foreach ( $this->keys() as $key ) {
			/**
			 * @var InputType $item
			 */
			$item = $this->get($key);
			if ($item instanceof InputTypeCollection) {
				$names = array_merge($names, $item->names($item->getName() . '.'));
			} else {
				$names[] = $root . $item->getName();
			}
		}
		return $names;
	}

	/**
	 * Gets all the input cast types
	 *
	 * @return array
	 */
	public function casts() {
		$casts = [];
		foreach ( $this->keys() as $key ) {
			/**
			 * @var Inputable $item
			 */
			$item = $this->get($key);

			if ($item instanceof InputTypeCollection) {
				foreach ($item->casts() as $name => $rule) {
					$rules["$key.$name"] = $rule;
				}
			} elseif (method_exists($item, 'cast')) {
				$casts[ $item->getName() ] = call_user_func([$item, 'cast'], $item);
			}
		}

		return $casts;
	}

	/**
	 * Set the entity attached to each of the inputs
	 *
	 * @param Entity $entity
	 */
	public function setEntity(Entity $entity) {
		foreach ($this->keys() as $key) {
			/**
			 * @var InputType $item
			 */
			$item = $this->get($key);

			$item->setEntity($entity);
		}
	}

	/**
	 * Insert a new input
	 *
	 * @param $key
	 * @param Inputable $type
	 */
	public function insert($key, Inputable $type)
	{
		$type->setName($key);
		$this->put($key, $type);
	}

	/**
	 * Get the inputs in this collection
	 *
	 * @return array
	 */
	public function inputs()
	{
		$items = [];
		foreach ( $this->all() as $item ) {
			$items[] = $item;
		}
		return $items;
	}

}