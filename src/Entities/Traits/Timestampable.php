<?php

namespace Foundry\Core\Entities\Traits;

use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Timestampable
 *
 * @package Foundry\Core\Traits
 */
trait Timestampable
{
	/**
	 * @ORM\Column(name="created_at", type="datetime", nullable=false)
	 * @var Carbon
	 */
	protected $created_at;

	/**
	 * @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 * @var Carbon
	 */
	protected $updated_at;

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
