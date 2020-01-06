<?php

namespace Foundry\Core\Inputs\Types\Traits;

/**
 * Trait HasSortable
 * @package Foundry\Core\Inputs\Types\Traits
 * @deprecated This is no longer used
 */
trait HasSortable {

    /**
     * @return bool
     * @deprecated This is no longer used
     */
	public function isSortable()
	{
		return (bool) $this->getAttribute('sortable', false);
	}

    /**
     * @param bool $sortable
     * @return $this
     * @deprecated This is no longer used
     */
	public function setSortable(bool $sortable)
	{
		$this->setAttribute('sortable', $sortable);

		return $this;
	}

}
