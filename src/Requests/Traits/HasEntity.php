<?php

namespace Foundry\Core\Requests\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasEntity {

	/**
	 * @var null|Model|object
	 */
	protected $entity = null;

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity( $entity ) {
		$this->entity = $entity;
	}
}
