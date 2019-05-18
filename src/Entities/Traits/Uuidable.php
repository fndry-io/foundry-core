<?php

namespace Foundry\Core\Entities\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Trait Uuidable
 *
 * @package Foundry\Core\Traits
 */
trait Uuidable
{
	/**
	 * @var \Ramsey\Uuid\UuidInterface
	 *
	 * @ORM\Column(type="uuid", unique=true)
	 */
	protected $uuid;

	/**
	 * @return mixed
	 */
	public function getUuid() {
		return $this->uuid;
	}

	/**
	 *
	 */
	public function setUuid( ): void {
		$this->uuid = Uuid::uuid4()->toString();
	}

}
