<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasMinMax {

	/**
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setMin( $value = null, $add_rule = true ) {
		$this->setAttribute('min', $value);
		if ( $add_rule && method_exists( $this, 'addRule' ) && $value !== null ) {
			$this->addRule( 'min:' . $value );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMin() {
		return $this->getAttribute('min');
	}

	/**
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setMax( $value = null, $add_rule = true ) {
		$this->setAttribute('max', $value);
		if ( $add_rule && method_exists( $this, 'addRule' ) && $value !== null ) {
			$this->addRule( 'max:' . $value );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMax() {
		return $this->getAttribute('max');
	}


}