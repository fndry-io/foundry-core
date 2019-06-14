<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Entityable;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasErrors;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasRules;
use Foundry\System\Entities\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


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
		HasRules
	;

	protected $request;

	/**
	 * @var Entity
	 */
	protected $entity;

	/**
	 * @var InputType[]
	 */
	protected $inputs;

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
	 * Attach an input collection to this Form
	 *
	 * @param Entity $entity
	 *
	 * @return $this
	 */
	public function setEntity( Entity &$entity = null ) {
		$this->entity = $entity;
		return $this;
	}

	/**
	 * @return Entity|null
	 */
	public function getEntity(): Entity {
		return $this->entity;
	}

	public function attachInputCollection( $collection ) {
		/**
		 * @var Collection $collection
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
		if ( $this->entity ) {
			foreach ( $inputs as &$input ) {
				if ( ! $input->hasEntity() ) {
					/**
					 * @var InputType $input
					 */
					$input->setEntity( $this->entity );
				}
			}
		}
		foreach ( $inputs as &$input ) {
			/**
			 * @var InputType $input
			 */
			$this->inputs[ $input->getName() ] = $input;
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
		if ( $this->entity ) {
			return object_get( $this->entity, $key );
		}

		return null;
	}

	/**
	 * Set values
	 *
	 * @param $values
	 *
	 * @return $this
	 */
	public function setValues( $values = [] ) {
		foreach ( $values as $name => $value ) {
			if ( $input = $this->getInput( $name ) ) {
				/**@var InputType $input */
				$input->setValue( $value );
			}
		}

		return $this;
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
	 * @return \Illuminate\Contracts\Support\MessageBag|null
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
		foreach ( $this->rules as $key => $rules ) {
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
		if ( $request && $request->session()->has( 'errors' ) && $request->session()->get( 'errors' )->hasBag( 'default' ) ) {
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
		if ( $this->inputs[ $name ] ) {
			return $this->inputs[ $name ];
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

		return $json;
	}

}
