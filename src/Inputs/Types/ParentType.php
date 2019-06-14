<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Children;
use Foundry\Core\Inputs\Types\Traits\HasChildren;

abstract class ParentType extends BaseType implements Children {

	use HasChildren;

	public function jsonSerialize(): array {
		$json = parent::jsonSerialize();

		if (!empty($this->children)) {
			$json['children'] = [];
			foreach ($this->children as $child) {
				$json['children'][] = $child->toArray();
			}
		}

		return $json;
	}
}