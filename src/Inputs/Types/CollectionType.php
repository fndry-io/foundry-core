<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasButtons;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;
use Foundry\Core\Inputs\Types\Traits\HasName;

class CollectionType extends ParentType {

	use HasMinMax,
		HasMultiple,
		HasButtons,
		HasName;

	public function __construct( $name ) {
		parent::__construct();
		$this->setName( $name );
		$this->setType( 'collection' );
	}

}