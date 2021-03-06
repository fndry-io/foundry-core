<?php

namespace Foundry\Core\Inputs;

use Foundry\Core\Support\InputTypeCollection;

class SimpleInputs extends Inputs {

	/**
	 * The types to associate with the input
	 *
	 * @return InputTypeCollection
	 */
	function types(): InputTypeCollection {
		return $this->types;
	}
}
