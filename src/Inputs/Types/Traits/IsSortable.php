<?php

namespace Foundry\Core\Inputs\Types\Traits;


trait IsSortable {

	protected $sortable = false;

	/**
	 * Get or set the sortable property
	 *
	 * @param bool|null $sort Leave null to get the current value
	 *
	 * @return $this|bool|null
	 */
	public function isSortable(bool $sort = null)
	{
		if (is_null($sort)) {
			return $this->sortable;
		} else {
			$this->sortable = $sort;
		}
		return $this;
	}

}