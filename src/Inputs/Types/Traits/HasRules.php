<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Contracts\Validation\Rule;

trait HasRules {

	/**
	 * @return string|array
	 */
	public function getRules() {
		if ( method_exists($this, 'isRequired') ) {
			if ( $this->isRequired() ) {
				$this->addRule( 'required' );
			} else {
				$this->addRule( 'nullable' );
			}
		}
		if ( isset( $this->min ) && $this->min !== null ) {
			$this->addRule( 'min:' . $this->min );
		}
		if ( isset( $this->max ) && $this->max !== null ) {
			$this->addRule( 'max:' . $this->max );
		}
		if ( method_exists( $this, 'getOptions' ) ) {
			$options = $this->getOptions();
			if ( is_array( $options ) && !empty($options) ) {

				$options = array_keys($this->getOptions());

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

		return $this->getAttribute('rules');
	}

	/**
	 * @param string|array $rules
	 *
	 * @return $this
	 */
	public function setRules( $rules = null ) {
		if ( is_string( $rules ) ) {
			$rules = explode( '|', $rules );
		}
		if ( $rules ) {
			foreach ( $rules as $key => $value ) {
				$this->addRule( $value, $key );
			}
		}
		return $this;
	}

	/**
	 * Adds a rule to the rules
	 *
	 * @param string|Rule $rule The rule to add
	 * @param null $key If the key given is a string, it will use it, if an integer it will ignore and just add the rule to the existing array
	 *
	 * @return $this
	 */
	public function addRule( $rule, $key = null ) {
		if (!$this->ruleExists($rule)) {
			if ( !isset($this->attributes['rules']) || empty( $this->attributes['rules'] ) ) {
				$this->attributes['rules'] = [];
			}
			if ( $key && is_string( $key ) ) {
				$this->attributes['rules'][ $key ] = $rule;
			} else {
				$this->attributes['rules'][] = $rule;
			}
		}

		return $this;
	}

	/**
	 * Removes rules based on their value
	 *
	 * @param mixed ...$rules
	 *
	 * @return $this
	 */
	public function removeRules( ...$rules ) {
		if ( ! empty( $this->attributes['rules'] ) ) {
			foreach ( $rules as $rule ) {
				$index = array_search( $rule, $this->attributes['rules'] );
				if ( $index !== false ) {
					unset( $this->attributes['rules'][ $index ] );
				}
			}
		}
		return $this;
	}

	/**
	 * Does the specified rule exist
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public function ruleExists($rule)
	{
		return (isset($this->attributes['rules']) && in_array($rule, $this->attributes['rules']));
	}
}