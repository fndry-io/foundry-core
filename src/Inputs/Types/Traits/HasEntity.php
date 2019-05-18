<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\System\Entities\Entity;

trait HasEntity {

	/**
	 * @var Entity
	 */
	protected $entity;

	/**
	 * @return null|Entity
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param Entity|null $entity
	 *
	 * @return $this
	 */
	public function setEntity( Entity &$entity = null ) {
		$this->entity = $entity;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasEntity(): bool {
		return ! ! ( $this->entity );
	}

	public function isFillable() {
		if ( $this->hasEntity() ) {
			return $this->getEntity()->isFillable( $this->getName() );
		}

		return true;
	}

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

	public function isHidden() {
		if ( $this->hasEntity() ) {
			$hidden = $this->getEntity()->getHidden();
			if ( in_array( $this->getName(), $hidden ) ) {
				return true;
			}
		}
		return false;
	}

	abstract public function getName() : string;

}