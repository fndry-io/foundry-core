<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Illuminate\Support\Str;

/**
 * Class Tab Type
 *
 * This will generate a tab which should be added to a tabs type
 *
 * @package Foundry\Requests\Types
 */
class TabType extends ParentType {

	use HasLabel,
		HasId;

	public function __construct( $label, $id = null ) {
		parent::__construct();
		$this->setType( 'tab' );
		$this->setLabel( $label );
		$id = $id ? $id : Str::camel( Str::slug( $label ) . 'Tab' );
		$this->setId( $id );
	}

}
