<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Support\Arr;

trait HasOptions {

	use HasMultiple;

	/**
	 * @return mixed
	 */
	public function isExpanded() : bool {
		return (bool) $this->getAttribute('expanded', false);
	}

	/**
	 * @param mixed $expanded
	 *
	 * @return $this
	 */
	public function setExpanded( bool $expanded = true ) {
		$this->setAttribute('expanded', $expanded);

		return $this;
	}

	/**
	 * @param null $value
	 *
	 * @return null|array
	 */
	public function getOptions($value = null) {

		$options = $this->getAttribute('options');

		if ( is_callable( $options ) ) {
			$call = $options;
			$this->setOptions($call(null, $value));
		}
		return $this->getAttribute('options');
	}

	/**
	 * @param array|\Closure $options
	 *
	 * @return $this
	 */
	public function setOptions( $options = null ) {
		if ($options && $first = Arr::first($options)) {
			if (!is_array($first)) {
				$_options = [];
				foreach ($options as $value => $text) {
					$_options[] = [
						$this->getValueKey() => $value,
						$this->getTextKey() => $text
					];
				}
				$options = $_options;
			}
		}
		$this->setAttribute('options', $options);

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

		$value = null;
		if (method_exists($this, 'getValue')) {
			$value = $this->getValue();
		}

		if ( is_array( $value ) ) {
			return in_array( $key, $value );
		} elseif ( is_string( $value ) ) {
			return $key == $value;
		}

		return false;
	}

	public function setEmpty( $value = null ) {
		$this->setAttribute('empty', $value);

		return $this;
	}

	public function getEmpty( ) {
		return $this->getAttribute('empty');
	}

	public function getEmptyLabel( $default = null ) {

		$empty = $this->getEmpty();

		if ( $empty === true ) {
			if ( $default !== null ) {
				return $default;
			} else {
				return __( 'Select...' );
			}
		} elseif ( is_string( $empty ) ) {
			return $empty;
		}
	}

	public function hasEmptyOption() {
		$empty = $this->getEmpty();
		return ! ! ( $empty );
	}

	public function setTextKey($key, $join = ' ')
	{
		if (is_array($key)) {
			$this->setAttribute('textKey', ['fields' => $key, 'join' => $join]);
		} else {
			$this->setAttribute('textKey', $key);
		}
		return $this;
	}

	public function getTextKey()
	{
		return $this->getAttribute('textKey');
	}

	public function setValueKey($key)
	{
		$this->setAttribute('valueKey', $key);
		return $this;
	}

	public function getValueKey()
	{
		return $this->getAttribute('valueKey');
	}

	public function setGroupKey($key)
	{
		$this->setAttribute('groupKey', $key);
		return $this;
	}

	public function getGroupKey()
	{
		$this->getAttribute('groupKey');
	}


}