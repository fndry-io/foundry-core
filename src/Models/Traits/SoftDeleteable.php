<?php

namespace Foundry\Core\Models\Traits;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait SoftDeleteable
 *
 * @property Carbon $deleted_at
 *
 * @package Foundry\Core\Models\Traits
 */
trait SoftDeleteable
{
	use SoftDeletes;

	/**
	 * @return DateTime
	 */
	public function getDeletedAt()
	{
		return $this->deleted_at;
	}

	/**
	 * @param DateTime|null $deleted_at
	 */
	public function setDeletedAt(\DateTime $deleted_at = null)
	{
		$this->deleted_at = $deleted_at;
	}

	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deleted_at && new DateTime('now') >= $this->deleted_at;
	}
}
