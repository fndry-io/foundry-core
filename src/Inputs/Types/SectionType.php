<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasDescription;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasTitle;

/**
 * Class Type
 *
 * @package Foundry\Requests\Types
 */
class SectionType extends ParentType {

	use HasId,
		HasClass,
		HasTitle,
		HasDescription;

	/**
	 * Type of the input to display
	 *
	 * @var $type
	 */
	protected $type;

	/**
	 * SectionType constructor.
	 *
	 * @param string $title
	 * @param string|null $description
	 * @param string|null $id
	 */
	public function __construct( string $title, string $description = null, string $id = null ) {
		$this->setType( 'section' );

		$this->setTitle( $title );
		$this->setDescription( $description );
		$id = $id ? $id : camel_case( str_slug( $title ) . 'Section' );
		$this->setId( $id );
	}

}
