<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Entities\Entity;

trait HasEntity {

	/**
	 * @var null|Entity|object
	 */
	protected $entity = null;

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity( $entity ) {
		$this->entity = $entity;
	}
}