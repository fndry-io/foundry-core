<?php

namespace Foundry\Core\Requests\Traits;

use Foundry\Core\Entities\Contracts\EntityInterface;

trait HasEntity {

	/**
	 * @var null|EntityInterface|object
	 */
	protected $entity = null;

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity( $entity ) {
		$this->entity = $entity;
	}
}