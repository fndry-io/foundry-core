<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasId;

/**
 * Class Content Type
 *
 * @package Foundry\Requests\Types
 */
class TagType extends ParentType {

	use HasId,
		HasClass
		;

	protected $tag;

	protected $content;

	/**
	 * SectionType constructor.
	 *
	 * @param string $tag
	 * @param string|null $id
	 */
	public function __construct( string $tag, $content = null, string $id = null ) {
		$this->setType( 'tag' );
		$this->setTag( $tag );
		$this->setContent( $content );
		$this->setId( $id );
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function setTag($tag)
	{
		$this->tag = $tag;
		return $this;
	}


	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

}
