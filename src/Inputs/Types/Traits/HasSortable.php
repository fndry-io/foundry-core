<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait HasSortable {

	public function isSortable()
	{
		return (bool) $this->getAttribute('sortable', false);
	}

	public function setSortable(bool $sortable)
	{
		$this->setAttribute('sortable', $sortable);

		return $this;
	}

}