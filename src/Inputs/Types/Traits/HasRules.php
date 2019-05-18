<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\InputType;
use Illuminate\Contracts\Validation\Rule;

trait HasRules {

	/**
	 * Rules to display
	 *
	 * @var array $rules
	 */
	protected $rules = null;

	/**
	 * @return string|array
	 */
	public function getRules() {
		if ( isset( $this->required ) ) {
			if ( $this->required ) {
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
		if ( isset( $this->options ) ) {
			if ( is_array( $this->options ) && !empty($this->options) ) {

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


		return $this->rules;
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
		$this->rules = $rules;

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
		if ( empty( $this->rules ) ) {
			$this->rules = [];
		}
		if ( $key && is_string( $key ) ) {
			$this->rules[ $key ] = $rule;
		} else {
			$this->rules[] = $rule;
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
		if ( ! empty( $this->rules ) ) {
			foreach ( $rules as $rule ) {
				$index = array_search( $rule, $this->rules );
				if ( $index !== false ) {
					unset( $this->required[ $index ] );
				}
			}
		}

		return $this;
	}
}