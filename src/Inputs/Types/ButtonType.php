<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
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
		HasAction
		;

	public function __construct(
		string $label,
		string $action = null,
		string $title = null,
		array $query = [],
		string $method = 'GET',
		string $id = null,
		string $type = 'action'
	) {
		parent::__construct();
		$this->setLabel( $label );
		$this->setAction( $action );
		$this->setTitle( $title );
		$this->setQuery( $query );
		$this->setMethod( $method );
		$this->setType( $type );
		$this->setId( $id );
	}

}
