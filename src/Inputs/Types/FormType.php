<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Entityable;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Contracts\Referencable;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasErrors;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasRules;
use Foundry\Core\Inputs\Types\Traits\HasTitle;
use Foundry\Core\Entities\Contracts\HasVisibility;
use Foundry\Core\Support\InputTypeCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class FormRow
 *
 * @package Foundry\Requests\Types
 */
class FormType extends ParentType implements Entityable {

	use HasName,
		HasClass,
		HasId,
		HasButtons,
		HasErrors,
		HasRules,
		HasTitle
	;

	protected $request;

	/**
	 * @var Arrayable|HasVisibility
	 */
	protected $entity;

	/**
	 * @var InputType[]
	 */
	protected $inputs;

	/**
	 * @var array
	 */
	protected $values = [];

	/**
	 * FormType constructor.
	 *
	 * @param $name
	 * @param null $id
	 */
	public function __construct( $name, $id = null ) {
		parent::__construct();
		$this->setType( 'form' );
		$this->setName( $name );
		$this->setId( $id );
		$this->setAttribute('method', 'POST');
	}

	public function setAction( $action ) {
		$this->setAttribute('action', $action);

		return $this;
	}

	public function getAction() {
		return $this->getAttribute('action');
	}

	public function setMethod( $method ) {
		$this->setAttribute('method', $method);

		return $this;
	}

	public function getMethod() {
		return $this->getAttribute('method');
	}

	public function setEncoding( $encoding ) {
		$this->setAttribute('encoding', $encoding);

		return $this;
	}

	public function getEncoding() {
		$this->getAttribute('encoding');
	}

	/**
	 * Set the entity for this form
	 *
	 * The object must be an instance of Arrayable
	 *
	 * @param Arrayable $entity
	 *
	 * @return $this
	 */
	public function setEntity( Arrayable $entity = null ) {
		$this->entity = $entity;
		return $this;
	}

	/**
	 * @return Arrayable|object|null
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @return bool
	 */
	public function hasEntity()
	{
		return !!($this->entity);
	}

	public function attachInputCollection( $collection ) {
		/**
		 * @var InputTypeCollection $collection
		 */
		$this->attachInputs( ...array_values( $collection->all() ) );

		return $this;
	}

	/**
	 * Attached a series of inputs to this Form
	 *
	 * @param Inputable ...$inputs
	 *
	 * @return $this
	 */
	public function attachInputs( Inputable ...$inputs ) {

		foreach ( $inputs as &$input ) {

			$input->setForm($this);

            /**
			 * @var InputType $input
			 */
			$this->inputs[ $input->getName() ] = $input;

            /**
             * If a reference type and have an entity, we we need to get the reference value and set it to the input
             */
            if ($this->hasEntity() && $input instanceof Referencable) {
                $reference = object_get($this->entity, $input->getName());
                $input->setReference($reference);
            }

        }

		return $this;
	}

	/**
	 * Get the attached inputs
	 *
	 * @return InputType[]
	 */
	public function getInputs() {
		return $this->inputs;
	}

	/**
	 * Get an attached input
	 *
	 * @param $name
	 *
	 * @return InputType|null
	 */
	public function getInput( $name ) {
		foreach ( $this->inputs as &$input ) {
			if ( $name === $input->getName() ) {
				return $input;
			}
		}

		return null;
	}

	/**
	 * Get the value for a given key on the entity
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function getValue( $key ) {
		$value = null;
		if ( $this->entity ) {
			$value = object_get($this->entity, $key);
		}
		$_value = Arr::get($this->values, $key);
		if ($_value !== null) {
			$value = $_value;
		}
		return $value;
	}

	/**
	 * Set the value of the key
	 *
	 * @param $key
	 * @param $value
	 */
	public function setValue($key, $value)
	{
		Arr::set($this->values, $key, $value);
	}

	/**
	 * Set values
	 *
	 * @param $values
	 *
	 * @return $this
	 */
	public function setValues( $values = [] ) {
        foreach($this->getInputs() as $input) {
            $value = Arr::get($values, $input->getName(), null );
            $input->setValue($value);
        }
		return $this;
	}

	/**
	 * Get the values for the form
	 *
	 * @return array
	 */
	public function getValues() {
		$values = [];
		foreach($this->getInputs() as $input) {
			if ($input->isVisible() && !$input->isHidden()) {
				$value = $input->getValue();
				if ($value === null) {
					$value = $input->getDefault();
				}
				Arr::set($values, $input->getName(), $value );
			}
		}

        return array_merge($this->values, $values);
	}

	/**
	 * Get the fields visible state off the entity
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function isVisible($key)
	{
		if ($this->entity && $this->entity instanceof HasVisibility) {
            $entity = $this->getEntity();
		    if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $key = array_pop($parts);
                foreach($parts as $part){
                    if ($entity->{$part} && $entity->{$part} instanceof HasVisibility) {
                        $entity = $entity->{$part};
                    } else {
                        return true;
                    }
                }
            }
            return $entity->isVisible($key);
		}
		return true;
	}

	/**
	 * Get the fields hidden state off the entity
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function isHidden($key)
	{
		if ($this->entity && $this->entity instanceof HasVisibility) {
            $entity = $this->getEntity();
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $key = array_pop($parts);
                foreach($parts as $part){
                    if ($entity->{$part} && $entity->{$part} instanceof HasVisibility) {
                        $entity = $entity->{$part};
                    } else {
                        return false;
                    }
                }
            }
            return $entity->isHidden($key);
		}
		return false;
	}

	/**
	 * Create a row and add inputs to it
	 *
	 * @param Inputable[] $types The inputs to add
	 *
	 * @return $this
	 */
	public function addInputRow( Inputable ...$types ) {
		$this->addChildren( ( new RowType() )->addChildren( ...$types ) );

		return $this;
	}

	/**
	 * Get the errors for the invalid input
	 *
	 * @param $key
	 *
	 * @return MessageBag|null
	 */
	public function getInputError( $key ) {
		if ( $input = $this->getInput( $key ) ) {
			if ( $input->hasErrors() ) {
				return $input->getErrors();
			}
		}

		return null;
	}

	/**
	 * Determine if a input is invalid
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function isInputInvalid( $key ) {
		if ( $input = $this->getInput( $key ) ) {
			return $input->hasErrors();
		}

		return null;
	}

	/**
	 * Return the number of inputs in this form
	 *
	 * @return int
	 */
	public function inputCount(): int {
		return count( $this->inputs );
	}

	/**
	 * Sets the rules for the form and its inputs
	 *
	 * @param $rules
	 *
	 * @return $this
	 */
	public function setRules( $rules = [] ) {
		$this->rules = $rules;
		foreach ( $this->getRules() as $key => $rules ) {
			if ( $input = $this->getInput( $key ) ) {
				/**@var InputType $input */
				$input->setRules( $rules );
			}
		}
		return $this;
	}

	/**
	 * Set the request
	 *
	 * @param Request|null $request
	 *
	 * @return $this
	 */
	public function setRequest( Request $request = null ) {
		$this->request = $request;
		if ( $request && $request->hasSession() && $request->session()->has( 'errors' ) && $request->session()->get( 'errors' )->hasBag( 'default' ) ) {
			$this->setErrors( $request->session()->get( 'errors' )->getBag( 'default' ) );
		}

		return $this;
	}

	/**
	 * @param bool $value
	 *
	 * @return $this
	 */
	public function setInline(bool $value = true){
		$this->setAttribute('inline', $value);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function isInline()
	{
		return $this->getAttribute('inline');
	}

	/**
	 * @param $name
	 *
	 * @return InputType|null
	 */
	public function get( $name ) {
		if ( $input = Arr::get($this->inputs, $name, null)) {
			return $input;
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		$json = parent::jsonSerialize();

		if (!empty($this->buttons)) {
			$json['buttons'] = [];
			foreach ($this->buttons as $button) {
				$json['buttons'][] = $button->toArray();
			}
		}
		if ($this->inputs) {
			$json['values'] = $this->getValues();
		}

		return $json;
	}

}
