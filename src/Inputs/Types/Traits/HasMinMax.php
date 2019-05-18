<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasMinMax {

	protected $max;

	protected $min;

	/**
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setMin( $value = null ) {
		$this->min = $value;
		if ( method_exists( $this, 'addRule' ) && $value !== null ) {
			$this->addRule( 'min:' . $value );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setMax( $value = null ) {
		$this->max = $value;
		if ( method_exists( $this, 'addRule' ) && $value !== null ) {
			$this->addRule( 'max:' . $value );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMax() {
		return $this->max;
	}


}