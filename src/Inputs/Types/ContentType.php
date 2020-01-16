<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Illuminate\Support\Str;

/**
 * Class ContentType
 *
 * @package Foundry\Requests\Types
 */
class ContentType extends BaseType {

	use HasClass;

	/**
	 * Type of the input to display
	 *
	 * @var $type
	 */
	protected $type;

	/**
	 * SectionType constructor.
	 *
	 * @param string $content
	 */
	public function __construct( string $content ) {
		parent::__construct();
		$this->setType( 'content' );
		$this->setContent( $content );
	}

	public function setContent(string $content)
    {
        $this->setAttribute('content', $content);
        return $this;
    }

}
