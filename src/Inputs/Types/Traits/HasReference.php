<?php

namespace Foundry\Core\Inputs\Types\Traits;


use Foundry\Core\Inputs\Types\ButtonType;

trait HasReference {

	/**
	 * @var string|object $reference
	 */
	protected $reference;


	/**
	 * The reference
	 *
	 * @param string|object $reference
	 *
	 * @return $this
	 */
	public function setReference($reference)
	{
		$this->reference = $reference;
		return $this;
	}

	/**
	 * The reference string or object
	 *
	 * @return null|string|object
	 */
	public function getReference()
	{
		return $this->reference;
	}

	/**
	 * Json serialise field
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {

		$this->reference = $this->getReferenceObject();
		return parent::jsonSerialize();
	}

	public function addButton($label, $request, $title, $type)
	{
		$this->setButtons(
			( new ButtonType( $label, $request, $title ) )->setType( $type )
		);
		return $this;
	}


	public function hasReference(): bool
	{
		$reference = $this->getReference();
		if (is_object($reference) || ($this->hasModel() && $reference = object_get($this->getModel(), $reference))) {
			return true;
		}
		return false;
	}

	public function getReferenceObject()
	{
		//Do we have a reference
		//Is it a string, meaning we need to locate it on the current model
		$reference = $this->getReference();
		if ($reference && is_string($reference) && $this->hasModel() && $reference = object_get($this->getModel(), $reference)) {
			return $reference;
		}elseif (is_object($reference))
            return $reference;

		return null;
	}

	public function getRouteParams() : array
	{
		$params = [];
		$value = null;
		$key = $this->getReference();
		if ($this->hasModel() && $reference = object_get($this->getModel(), $key)) {
			$value = $reference->getKey();
			$placeholder = str_slug((new \ReflectionClass($reference))->getShortName(), '_');
			$params[$placeholder] = $value;
		}
		return $params;
	}



}
