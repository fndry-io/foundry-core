<?php

namespace Foundry\Core\Inputs\Types\Traits;

use Foundry\Core\Inputs\Types\ButtonType;

trait HasButtons {

	/**
	 * Buttons to add to the form input
	 *
	 * @var ButtonType[] The array of button types
	 */
	protected $buttons = [];

	/**
	 * Adds buttons to the input
	 *
	 * @param ButtonType ...$buttons
	 *
	 * @return $this
	 */
	public function setButtons( ButtonType ...$buttons ) {
		foreach ( $buttons as $button ) {
			$this->buttons[] = $button;
		}

		return $this;
	}

	/**
	 * Gets the buttons
	 *
	 * @return array
	 */
	public function getButtons(): array {
		return $this->buttons;
	}

	public function hasButtons() {
		return ! empty( $this->buttons );
	}

	/**
	 * Allows you to set the params attached to the buttons
	 *
	 * @param $params
	 *
	 * @return $this
	 */
	public function setButtonParams($params)
	{
		for ($i = 0; $i < count($this->buttons); $i++) {
			$this->buttons[$i]->setParams($params);
		}
		return $this;
	}

	public function withoutButtons()
    {
        $this->buttons = null;
        return $this;
    }

}
