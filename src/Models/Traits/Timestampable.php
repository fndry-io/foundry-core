<?php

namespace Foundry\Core\Models\Traits;

use Carbon\Carbon;
use DateTime;

/**
 * Trait Timestampable
 *
 * @package Foundry\Core\Traits
 */
trait Timestampable
{

		/**
	 * @return DateTime
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
	}

	/**
	 * @param Carbon|null $date
	 */
	public function setCreatedAt(Carbon $date = null)
	{
		$this->created_at = $date;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	/**
	 * @param Carbon|null $date
	 */
	public function setUpdatedAt(Carbon $date = null)
	{
		$this->updated_at = $date;
	}

}
