<?php
namespace Foundry\Core\Models\Traits;

use Foundry\Core\Entities\Contracts\IsNestedTreeable;

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