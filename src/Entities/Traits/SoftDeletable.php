<?php

namespace Foundry\Core\Entities\Traits;

use Carbon\Carbon;
use DateTime;

trait SoftDeletable
{
	/**
	 * @var DateTime
	 */
	protected $deleted_at;

	/**
	 * @return DateTime
	 */
	public function getDeletedAt()
	{
		return $this->deleted_at;
	}

	/**
	 * @param Carbon|null $deleted_at
	 */
	public function setDeletedAt(Carbon $deleted_at = null)
	{
		$this->deleted_at = $deleted_at;
	}

	/**
	 * Restore the soft-deleted state
	 */
	public function restore()
	{
		$this->deleted_at = null;
	}

	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deleted_at && new DateTime('now') >= $this->deleted_at;
	}
}
