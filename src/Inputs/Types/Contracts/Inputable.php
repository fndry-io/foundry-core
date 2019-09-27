<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Foundry\Core\Entities\Entity;
use Foundry\Core\Inputs\Types\FormType;

/**
 * Interface Inputable
 *
 * Inputable represents an input type that can receive data from user input in some way
 *
 * It also means the data can be linked to an entity and the name property of the inputable should map to the property
 * on the Entity.
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
	 * This should map to the Entity property name
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function setName(string $name);

	/**
	 * @param FormType $form
	 *
	 * @return mixed
	 */
	public function setForm(FormType &$form);

	/**
	 * @return FormType|null
	 */
	public function getForm(): ?FormType;

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