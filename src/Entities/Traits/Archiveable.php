<?php

namespace Foundry\Core\Entities\Traits;

use Carbon\Carbon;

/**
 * Trait Archiveable
 *
 * @package Foundry\Core\Traits
 */
trait Archiveable
{
	/**
	 * @var Carbon
	 */
	protected $archived_at;

	/**
	 * @param Carbon|null $archived_at
	 */
	public function setArchivedAt( $archived_at ): void {
		$this->archived_at = $archived_at;
	}

	/**
	 * @return Carbon
	 */
	public function getArchivedAt(): Carbon {
		return $this->archived_at;
	}

	/**
	 * Restore the soft-deleted state
	 */
	public function unArchive(){
		$this->archived_at = null;
	}

	/**
	 * @return bool
	 */
	public function isArchived()
	{
		return $this->archived_at && new \DateTime('now') >= $this->archived_at;
	}

}
