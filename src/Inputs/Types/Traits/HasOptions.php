<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\Contracts\Choosable;
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
	 * @param boolean $add_rule To automatically add an in rule for the available options
	 *
	 * @return $this
	 */
	public function setOptions( $options = null, $add_rule = false ) {
		$this->setAttribute('options', $options);

		if ( $add_rule ) {
			if ( is_array( $options ) && !empty($options) ) {
				$options = Arr::pluck($options, $this->getValueKey());
				if (isset($this->multiple) && $this->multiple) {
					$this->addRule( function ($attribute, $values, $fail) use ($options) {
						$values = (array) $values;
						foreach ($values as $value) {
							if (!in_array($value, $options)) {
								$fail($attribute.' is invalid.');
							}
						}
					} );
				} else {
					$this->addRule( \Illuminate\Validation\Rule::in( $options ) );
				}
			}
		}

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
