<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasId;

/**
 * Class FormView
 *
 * @package Foundry\Requests\Types
 */
class DocType extends ParentType {

	use HasId,
		HasClass;

	/**
	 * DocType constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id = null ) {
		parent::__construct();
		$this->setId( $id );
		$this->setType( 'doc' );
	}

	static function withChildren( BaseType ...$children )
	{
		return ( new static() )->addChildren( ...$children );
	}

}
