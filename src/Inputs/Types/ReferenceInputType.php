<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Contracts\Referencable;
use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasOptions;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\Core\Inputs\Types\Traits\HasQueryOptions;
use Foundry\Core\Inputs\Types\Traits\HasReference;
use Foundry\Core\Inputs\Types\Traits\HasRoute;
use Illuminate\Support\Arr;

/**
 * Class ReferenceType
 *
 * A reference type is used to set hasOne, hasMany, belongsTo, belongsToMany
 *
 * @package Foundry\Requests\Types
 */
class ReferenceInputType extends TextInputType implements Referencable, Castable {

	use HasButtons;
	use HasQueryOptions;
	use HasOptions;
	use HasReference;
	use HasRoute;
	use HasParams;


	/**
	 * Reference constructor
	 *
	 * @param string $name The field name
	 * @param string $label
	 * @param bool $required
	 * @param mixed $reference
	 * @param string $url The url to fetch the list of existing options
	 * @param null $value
	 * @param string $position
	 * @param string|null $rules
	 * @param string|null $id
	 * @param string|null $placeholder
	 * @param string $query_param
	 */
	public function __construct(
		string $name,
		string $label,
		bool $required = true,
		$reference,
		$url = null,
		$value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null,
		string $query_param = 'q'
	) {
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, 'reference' );
		$this->setUrl( $url );
		$this->setQueryParam( $query_param );
		$this->setReference($reference);
	}

	public function display( $value = null ) {
		$reference = $this->getReference();
		if (is_object($reference) || ($this->hasEntity() && $reference = object_get($this->getEntity(), $reference))) {
			if (is_object($reference)) {
				return $reference->label;
			}
		}
		return null;
	}

	public function getCastValue($value)
	{
		if (is_array($value)) {
			$valueKey = $this->getValueKey();
			return Arr::get($value, $valueKey);
		} else {
			return $value;
		}
	}

}
