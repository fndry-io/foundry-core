<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasDescription;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasTitle;
use Illuminate\Support\Str;

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
		parent::__construct();
		$this->setType( 'section' );

		$this->setTitle( $title );
		$this->setDescription( $description );
		$id = $id ? $id : Str::camel( Str::slug( $title ) . 'Section' );
		$this->setId( $id );
	}

}
