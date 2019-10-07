<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Entities\Contracts\HasIdentity;
use Foundry\Core\Inputs\Types\ButtonType;
use Illuminate\Support\Str;

trait HasReference {

	/**
	 * @var string|object $reference
	 */
	protected $reference;

	public function __HasReference(){
		$this->setValueKey('value');
		$this->setTextKey('text');
	}

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
		if (is_object($reference) && $reference instanceof HasIdentity) {
			$this->setValue($reference->getKey());
		}
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
		if (is_object($reference) || ($this->hasEntity() && $reference = object_get($this->getEntity(), $reference))) {
			return true;
		}
		return false;
	}

	public function getReferenceObject()
	{
		//Do we have a reference
		//Is it a string, meaning we need to locate it on the current model
		$reference = $this->getReference();
		if ($reference && is_string($reference) && $this->hasEntity() && $reference = object_get($this->getEntity(), $reference)) {
			return $reference;
		}elseif (is_object($reference)) {
			return $reference;
		}

		return null;
	}

	public function getRouteParams() : array
	{
		$params = [];
		$value = null;
		$key = $this->getReference();
		if ($this->hasEntity() && $reference = object_get($this->getEntity(), $key)) {
			//todo update this or implement equivalent in the Entity Abstract class
			$value = $reference->getKey();
			$placeholder = Str::slug((new \ReflectionClass($reference))->getShortName(), '_');
			$params[$placeholder] = $value;
		}
		return $params;
	}


}
