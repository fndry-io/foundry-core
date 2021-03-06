<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\Core\Inputs\Types\Traits\HasTitle;
use Foundry\Core\Inputs\Types\Traits\HasAction;

/**
 * Class Type
 *
 * @package Foundry\Requests\Types
 */
class ButtonType extends BaseType {

	use HasId,
		HasLabel,
		HasClass,
		HasTitle,
		HasAction,
		HasParams
		;

	public function __construct(
		string $label,
		string $action = null,
		string $title = null,
		array $params = [],
		string $method = 'GET',
		string $id = null,
		string $type = 'action'
	) {
		parent::__construct();
		$this->setLabel( $label );
		$this->setAction( $action );
		$this->setTitle( $title );
		$this->setParams( $params );
		$this->setMethod( $method );
		$this->setType( $type );
		$this->setId( $id );

		if ($type == 'submit') {
			$this->setVariant('primary');
		}
	}

	public function setVariant($variant){
		$this->setAttribute('variant', $variant);
		return $this;
	}

	public function getVariant(){
		return $this->getAttribute('variant');
	}

}
