<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasId;

/**
 * Class Tag Type
 *
 * This is used to create an HTML tag on the front end
 *
 * @package Foundry\Requests\Types
 */
class TagType extends ParentType {

	use HasId,
		HasClass
		;

	/**
	 * SectionType constructor.
	 *
	 * @param string $tag
	 * @param string|null $content
	 * @param string|null $id
	 */
	public function __construct( string $tag, string $content = null, string $id = null ) {
		parent::__construct();
		$this->setType( 'tag' );
		$this->setTag( $tag );
		$this->setContent( $content );
		$this->setId( $id );
	}

	public function getTag()
	{
		return $this->getAttribute('tag');
	}

	public function setTag($tag)
	{
		$this->setAttribute('tag', $tag);
		return $this;
	}

	public function getContent()
	{
		return $this->getAttribute('content');
	}

	public function setContent($content)
	{
		$this->setAttribute('content', $content);
		return $this;
	}

}
