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

    /**
     * Sets and Gets the pills property
     *
     * @param null|boolean $prop
     * @return $this|null|boolean
     */
	public function pills($prop = null)
    {
        if (is_null($prop)) {
            return $this->getAttribute('pills');
        } else {
            $this->setAttribute('pills', $prop);
            return $this;
        }
    }

    /**
     * Sets and Gets the fill property
     *
     * @param null|boolean $prop
     * @return $this|null|boolean
     */
    public function fill($prop = null)
    {
        if (is_null($prop)) {
            return $this->getAttribute('fill');
        } else {
            $this->setAttribute('fill', $prop);
            return $this;
        }
    }

    /**
     * Sets and Gets the justified property
     *
     * @param null|boolean $prop
     * @return $this|null|boolean
     */
    public function justified($prop = null)
    {
        if (is_null($prop)) {
            return $this->getAttribute('justified');
        } else {
            $this->setAttribute('justified', $prop);
            return $this;
        }
    }

    /**
     * Sets and Gets the card property
     *
     * @param null|boolean $prop
     * @return $this|null|boolean
     */
    public function card($prop = null)
    {
        if (is_null($prop)) {
            return $this->getAttribute('card');
        } else {
            $this->setAttribute('card', $prop);
            return $this;
        }
    }

}
