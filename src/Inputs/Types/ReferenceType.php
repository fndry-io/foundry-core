<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Contracts\Referencable;
use Foundry\Core\Inputs\Types\Traits\HasAutocomplete;
use Foundry\Core\Inputs\Types\Traits\HasEntity;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasReference;
use Foundry\Core\Inputs\Types\Traits\HasRoute;
use Foundry\Core\Inputs\Types\Traits\HasSortable;

/**
 * Class ReferenceType
 *
 * A reference type allows us to display what (an Entity) is being referenced or linked
 *
 * @package Foundry\Requests\Types
 */
class ReferenceType extends BaseType implements Referencable, Inputable {

	use HasReference,
		HasEntity,
		HasLabel,
		HasName,
		HasRoute,
		HasSortable,
		HasHelp,
		HasAutocomplete
	;

	/**
	 * Reference constructor
	 *
	 * @param mixed $reference
	 * @param string $label
	 * @param string $route
	 */
	public function __construct(
		string $reference,
		string $label,
		string $route = null
	) {
		parent::__construct();
		$this->setType( 'reference' );
		$this->setReference($reference);
		$this->setLabel( $label ? $label : $reference );
		$this->setRoute($route);

		$this->setAutocomplete(false);
	}

	public function display( $value = null ) {
		$reference = $this->getReference();
		if (is_object($reference) || ($this->hasEntity() && $reference = object_get($this->getEntity(), $reference))) {
			$entity = $this->getEntity();
			return $reference->label . (method_exists($entity, 'name') ? ' (' . $entity::name() . ')' : '');
		}
		return null;
	}

}
