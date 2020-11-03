<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasModel {

	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * @return Model
	 */
	public function getModel(): Model {
		return $this->model;
	}

	/**
	 * @param Model|null $model
	 *
	 * @return $this
	 */
	public function setModel( Model $model = null ) {
		$this->model = $model;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasModel(): bool {
		return ! ! ( $this->model );
	}

	public function isFillable() {
		if ( $this->hasModel() ) {
			return $this->getModel()->isFillable( $this->getName() );
		}

		return true;
	}

	public function isVisible() {
		if ( $this->hasModel() ) {
			$hidden  = $this->getModel()->getHidden();
			$visible = $this->getModel()->getVisible();
			if ( ! in_array( $this->getName(), $hidden ) && in_array( $this->getName(), $visible ) ) {
				return true;
			} elseif ( in_array( $this->getName(), $hidden ) ) {
				return false;
			}
		}

		return true;
	}

	public function isHidden() {
		if ( $this->hasModel() ) {
			$hidden = $this->getModel()->getHidden();
			if ( in_array( $this->getName(), $hidden ) ) {
				return true;
			}
		}
		return false;
	}

}
