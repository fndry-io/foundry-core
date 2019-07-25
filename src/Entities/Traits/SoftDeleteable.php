<?php

namespace Foundry\Core\Entities\Traits;

use DateTime;

trait SoftDeleteable
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
	 * @param DateTime|null $deleted_at
	 */
	public function setDeletedAt(\DateTime $deleted_at = null)
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
