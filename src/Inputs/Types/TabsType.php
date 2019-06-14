<?php

namespace Foundry\Core\Inputs\Types;

/**
 * Class Tabs Type
 *
 * This is a container type for the tab type
 *
 * The children added to this would only be tab types
 *
 * @package Foundry\Requests\Types
 */
class TabsType extends ParentType {

	/**
	 * TabsType constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->setType( 'tabs' );
	}

	static function withTabs( TabType ...$inputs ) {
		return ( new static() )->addChildren( ...$inputs );
	}

}
