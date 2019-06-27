<?php

namespace Foundry\Core\Support;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Entities\Entity;
use Illuminate\Support\Collection;

class InputTypeCollection extends Collection {

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
			} elseif ($item->getType() === 'checkbox' && $item->isMultiple()) {
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

	public function setEntity(Entity $entity) {
		foreach ($this->keys() as $key) {
			/**
			 * @var InputType $item
			 */
			$item = $this->get($key);

			$item->setEntity($entity);
		}
	}

	public function insert($key, Inputable $type)
	{
		$type->setName($key);
		$this->put($key, $type);
	}

	public function inputs()
	{
		$items = [];
		foreach ( $this->all() as $item ) {
			$items[] = $item;
		}
		return $items;
	}

}