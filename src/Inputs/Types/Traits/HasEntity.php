<?php

namespace Foundry\Core\Inputs\Types\Traits;


use Illuminate\Database\Eloquent\Model;

trait HasEntity {

	/**
	 * @var Model
	 */
	protected $entity;

	/**
	 * @return null|Model
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param Model|null $entity
	 *
	 * @return $this
	 */
	public function setEntity( $entity = null ) {
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
