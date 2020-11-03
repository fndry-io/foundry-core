<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Foundry\Core\Inputs\Inputs;

/**
 * Interface Inputable
 *
 * Inputable represents an input type that can receive data from user input in some way
 *
 * It also means the data can be linked to an model and the name property of the inputable should map to the property
 * on the Model.
 *
 * @package Foundry\Core\Inputs\Types\Contracts
 */
interface Inputable
{

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Set the name of the inputable
	 *
	 * This should map to the Model property name
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function setName(string $name);

	/**
	 * @param Inputs $inputs
	 *
	 * @return mixed
	 */
	public function setInputs(Inputs &$inputs);

	/**
	 * @return Inputs|null
	 */
	public function getInputs(): ?Inputs;

	/**
	 * The display value of the inputable
	 *
	 * Often a store value when displayed, such as telephone numbers or passwords, should be masked in some manner
	 *
	 * @param null $value
	 *
	 * @return mixed
	 */
	public function display($value = null);

}
