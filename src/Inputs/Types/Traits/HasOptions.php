<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasOptions {

	use HasMultiple;

	/**
	 * @var array|\Closure $options Available options
	 */
	protected $options = [];

	/**
	 * @var bool Determines if the options are expanded, such as in a series of checkboxes or radios
	 */
	protected $expanded = false;

	/**
	 * @var mixed The empty option. Null or false for none. True for default text or a string for the displayed text
	 */
	protected $empty;

	/**
	 * @return mixed
	 */
	public function isExpanded() : bool {
		return $this->expanded;
	}

	/**
	 * @param mixed $expanded
	 *
	 * @return $this
	 */
	public function setExpanded( bool $expanded = true ) {
		$this->expanded = $expanded;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions($value = null): array {

		if ( is_callable( $this->options ) ) {
			$call = $this->options;
			$this->options = $call(null, $value);
		}
		return $this->options;
	}

	/**
	 * @param array|\Closure $options
	 *
	 * @return $this
	 */
	public function setOptions( $options = null ) {
		$this->options = $options;

		return $this;
	}

	/**
	 * Determines if a value is selected
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function isOptionChecked( $key ) {
		if ( is_array( $this->value ) ) {
			return in_array( $key, $this->value );
		} elseif ( is_string( $this->value ) ) {
			return $key == $this->value;
		}

		return false;
	}

	public function setEmpty( $value = null ) {
		$this->empty = $value;

		return $this;
	}

	public function getEmpty( $value ) {
		return $this->empty;
	}

	public function getEmptyLabel( $default = null ) {
		if ( $this->empty === true ) {
			if ( $default !== null ) {
				return $default;
			} else {
				return __( 'Select...' );
			}
		} elseif ( is_string( $this->empty ) ) {
			return $this->empty;
		}
	}

	public function hasEmptyOption() {
		return ! ! ( $this->empty );
	}


}