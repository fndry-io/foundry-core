<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class FormRow
 *
 * @package Foundry\Requests\Types
 */
class RowType extends ParentType {

	public function __construct() {
		parent::__construct();
		$this->setType( 'row' );
	}

	static function withChildren( BaseType ...$inputs ) {
		return ( new static() )->addChildren( ...$inputs );
	}

}
