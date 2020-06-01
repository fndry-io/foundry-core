<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\In;

trait HasRules {

    protected $server_rules = [];

    protected $front_rules = [];

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

		return $this->getAttribute('rules');
	}

    /**
     * @return string|array
     */
    public function getRulesForServer() {
        $rules = $this->getRules();

        if (!empty($this->server_rules) && is_array($rules)) {
            $rules = array_merge($rules, $this->server_rules);
        }

        return $rules;
    }

    /**
     * @return string|array
     */
    public function getRulesForFrontend() {
        $rules = $this->getRules();

        if (!empty($this->front_rules) && is_array($rules)) {
            $rules = array_merge($rules, $this->front_rules);
        }

        return $rules;
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
		} else {
			$this->setAttribute('rules', null);
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
     * Adds a rule to the server only rules
     *
     * @param string|Rule $rule The rule to add
     * @param null $key If the key given is a string, it will use it, if an integer it will ignore and just add the rule to the existing array
     *
     * @return $this
     */
    public function addServerRule( $rule, $key = null ) {
        if (!$this->serverRuleExists($rule)) {

            if ( $key && is_string( $key ) ) {
                $this->server_rules[ $key ] = $rule;
            } else {
                $this->server_rules[] = $rule;
            }
        }

        return $this;
    }

    /**
     * Adds a rule to the front end only rules
     *
     * @param string|Rule $rule The rule to add
     * @param null $key If the key given is a string, it will use it, if an integer it will ignore and just add the rule to the existing array
     *
     * @return $this
     */
    public function addFrontRule( $rule, $key = null ) {
        if (!$this->frontRuleExists($rule)) {

            if ( $key && is_string( $key ) ) {
                $this->front_rules[ $key ] = $rule;
            } else {
                $this->front_rules[] = $rule;
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
	 * @param In $rule
	 *
	 * @return bool
	 */
	public function ruleExists($rule)
	{
		return (isset($this->attributes['rules']) && in_array($rule, $this->attributes['rules']));
	}

    /**
     * Does the specified rule exist
     *
     * @param In $rule
     *
     * @return bool
     */
    public function serverRuleExists($rule)
    {
        return (isset($this->server_rules) && in_array($rule, $this->server_rules));
    }

    /**
     * Does the specified rule exist
     *
     * @param In $rule
     *
     * @return bool
     */
    public function frontRuleExists($rule)
    {
        return (isset($this->front_rules) && in_array($rule, $this->front_rules));
    }
}
