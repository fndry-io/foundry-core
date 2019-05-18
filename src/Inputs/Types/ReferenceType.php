<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Contracts\Referencable;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasModel;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasReference;
use Foundry\Core\Inputs\Types\Traits\HasRoute;
use Foundry\Core\Inputs\Types\Traits\HasValue;
use Foundry\Core\Inputs\Types\Traits\IsSortable;

/**
 * Class RelatableType
 *
 * A relatable type allows us to display what is being related
 *
 * @package Foundry\Requests\Types
 */
class ReferenceType extends BaseType implements Referencable, Inputable {

	use HasReference,
		HasModel,
		HasLabel,
		HasName,
		HasRoute,
		IsSortable,
		HasHelp
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
		$this->setType( 'reference' );
		$this->setReference($reference);
		$this->setLabel( $label ? $label : $reference );
		$this->setRoute($route);
	}

	public function display( $value = null ) {
		$reference = $this->getReference();
		if (is_object($reference) || ($this->hasModel() && $reference = object_get($this->getModel(), $reference))) {
			$model = $this->getModel();
			return $reference->label . (method_exists($model, 'name') ? ' (' . $model::name() . ')' : '');
		}
		return null;
	}

}
