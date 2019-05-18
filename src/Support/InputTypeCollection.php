<?php

namespace Foundry\Core\Support;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\System\Entities\Entity;
use Illuminate\Support\Collection;

class InputTypeCollection extends Collection {

	static public function fromTypes($types)
	{
		$collection = new static();
		foreach ($types as $type) {
			/**
			 * @var Inputable $type
			 */
			$collection->put($type->getName(), $type);
		}
		return $collection;
	}

	public function rules() {
		$rules = [];
		foreach ( $this->all() as $item ) {
			/**
			 * @var InputType $item
			 */
			if ($item->getType() === 'checkbox' && $item->isMultiple()) {
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
		foreach ( $this->all() as $item ) {
			/**
			 * @var Inputable $item
			 */
			if (method_exists($item, 'cast')) {
				$casts[ $item->getName() ] = call_user_func([$item, 'cast'], $item);
			}
		}

		return $casts;
	}

	public function setEntity(Entity $entity) {
		foreach ($this->items as $item) {
			/**
			 * @var InputType $item
			 */
			$item->setEntity($entity);
		}
	}

	public function insert($key, Inputable $type)
	{
		$type->setName($key);
		$this->put($key, $type);
	}

}