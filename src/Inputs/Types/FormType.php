<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Inputs\Types\Contracts\Entityable;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasErrors;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\Core\Inputs\Types\Traits\HasRules;
use Foundry\Core\Inputs\Types\Traits\HasTitle;
use Foundry\Core\Entities\Contracts\HasVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;

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
		HasTitle,
        HasParams
	;

    /**
     * @var Request
     */
	protected $request;

	/**
	 * @var Arrayable|HasVisibility
	 */
	protected $entity;

	/**
	 * @var Inputs
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
	 * @return Arrayable|object|null
	 */
	public function getEntity() {
		return ($this->inputs) ? $this->inputs->getEntity() : null;
	}

	/**
	 * @return bool
	 */
	public function hasEntity()
	{
        return ($this->inputs) ? $this->inputs->hasEntity() : false;
	}

    /**
     * Attaches an Inputs class to the Form
     *
     * @param Inputs $inputs
     * @return $this
     */
	public function attachInputs( Inputs $inputs ) {
		$this->inputs = $inputs;
		return $this;
	}
//
//	/**
//	 * Attached a series of inputs to this Form
//	 *
//	 * @param Inputable ...$inputs
//	 *
//	 * @return $this
//	 */
//	public function attachInputs( Inputable ...$inputs ) {
//
//		foreach ( $inputs as &$input ) {
//
//			$input->setForm($this);
//
//            /**
//			 * @var InputType $input
//			 */
//			$this->inputs[ $input->getName() ] = $input;
//
//            /**
//             * If a reference type and have an entity, we we need to get the reference value and set it to the input
//             */
//            if ($this->hasEntity() && $input instanceof Referencable) {
//                $reference = object_get($this->entity, $input->getName());
//                $input->setReference($reference);
//            }
//
//        }
//
//		return $this;
//	}

	/**
	 * Get the attached inputs
	 *
	 * @return Inputs|null
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
	public function &getInput( $name ) {
		return $this->inputs->getType($name);
	}

	/**
	 * Get the value for a given key on the entity
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function getValue( $key ) {
		return ($this->inputs) ? $this->inputs->getValue($key) : null;
	}

	/**
	 * Set the value of the key
	 *
	 * @param $key
	 * @param $value
     * @throws \Exception
     */
	public function setValue($key, $value)
	{
	    if (!$this->inputs) {
	        throw new \Exception(sprintf('Inputs class not set on Form ?', self::class));
        }
	    $this->inputs->setValue($key, $value);
	}

	/**
	 * Set the values on the form
     *
     * Passthrough to the inputs object
	 *
     * @param array $values
     * @return $this
     * @throws \Exception
     */
	public function setValues( $values = [] ) {
        if (!$this->inputs) {
            throw new \Exception(sprintf('Inputs class not set on Form ?', self::class));
        }
        $this->inputs->setValue($values);
		return $this;
	}

	/**
	 * Get the values for the form
	 *
	 * @return array
	 */
	public function getValues() {
		return ($this->inputs) ? $this->inputs->values() : [];
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
		if ($this->hasEntity() && $this->getEntity() instanceof HasVisibility) {
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
        if ($this->hasEntity() && $this->getEntity() instanceof HasVisibility) {
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
		return ($this->inputs) ? $this->inputs->getTypes()->count() : 0;
	}

//	/**
//	 * Sets the rules for the form and its inputs
//	 *
//	 * @param $rules
//	 *
//	 * @return $this
//	 */
//	public function setRules( $rules = [] ) {
//		$this->rules = $rules;
//		foreach ( $this->getRules() as $key => $rules ) {
//			if ( $input =& $this->getInput( $key ) ) {
//				/**@var InputType $input */
//				$input->setRules( $rules );
//			}
//		}
//		return $this;
//	}

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
	 * @param $key
	 *
	 * @return InputType|CollectionInputType|null
     * @throws \Exception
     */
	public function &get( $key ) {

        if (!$this->inputs) {
            throw new \Exception(sprintf('Inputs class not set on Form ?', self::class));
        }

        if ($this->inputs->hasType( $key )) {
            return $this->inputs->getTypes()[$key];
        }

//        $value =& recursive_get_by_reference($this->inputs->getTypes(), $key);
        $value = $this->inputs->getTypes()->get($key);

        if ($value === null) {
            throw new \Exception(sprintf('Input "%s" not found in %s', $key, get_class($this->inputs)));
        }


        return $value;
	}

    /**
     * Returns if a input exists
     *
     * @param $key
     * @return bool
     */
	public function hasInput($key)
    {
        return ($this->inputs) ? $this->inputs->hasType($key) : false;
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
			$json['values'] = $this->inputs->all();
		}

        if ($this->inputs) {
            $json['inputs'] = $this->inputs->getTypes()->all();
        }

		return $json;
	}

}
