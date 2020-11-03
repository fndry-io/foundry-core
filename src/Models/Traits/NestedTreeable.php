<?php
namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Contracts\IsNestedTreeable;

trait NestedTreeable {

	public function setParent(IsNestedTreeable $parent = null)
	{
		$this->parent = $parent;
	}

	public function getParent()
	{
		return $this->parent;
	}
}
