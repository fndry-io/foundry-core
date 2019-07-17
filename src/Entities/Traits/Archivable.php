<?php

namespace Foundry\Core\Entities\Traits;

use Carbon\Carbon;

/**
 * Trait Archivable
 *
 * @package Foundry\Core\Traits
 */
trait Archivable
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

}
