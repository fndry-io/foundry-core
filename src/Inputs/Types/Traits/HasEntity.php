<?php

namespace Foundry\Core\Inputs\Types\Traits;


use Foundry\Core\Entities\Entity;
use Foundry\Core\Models\Model;
use Illuminate\Support\Arr;

trait HasEntity {

	/**
	 * @var Entity|Model
	 */
	protected $entity;

	/**
	 * @return null|Entity|Model
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param Entity|Model|null $entity
	 *
	 * @return $this
	 */
	public function setEntity( &$entity = null ) {
		$this->entity = $entity;
		$value = $this->entity->get($this->getName());
		$this->setValue($value);
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasEntity(): bool {
		return ! ! ( $this->entity );
	}

	/**
	 * @return bool
	 */
	public function isFillable() {
		if ( $this->hasEntity() ) {
			return $this->getEntity()->isFillable( $this->getName() );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isVisible() {
		if ( $this->hasEntity() ) {
			$hidden  = $this->getEntity()->getHidden();
			$visible = $this->getEntity()->getVisible();
			if ( ! in_array( $this->getName(), $hidden ) && in_array( $this->getName(), $visible ) ) {
				return true;
			} elseif ( in_array( $this->getName(), $hidden ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		if ( $this->hasEntity() ) {
			$hidden = $this->getEntity()->getHidden();
			if ( in_array( $this->getName(), $hidden ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return string
	 */
	abstract public function getName() : string;

}