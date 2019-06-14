<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasMinMax {

	/**
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setMin( $value = null ) {
		$this->setAttribute('min', $value);
		if ( method_exists( $this, 'addRule' ) && $value !== null ) {
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
	public function setMax( $value = null ) {
		$this->setAttribute('max', $value);
		if ( method_exists( $this, 'addRule' ) && $value !== null ) {
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