<?php

namespace Foundry\Core\Inputs\Types\Contracts;

/**
 * Interface Referencable
 *
 * This represents an object type which references an Entity
 *
 * It is typically used when attaching or linking entities to an input in a form, like an Address entity to a Contact
 *
 * @package Foundry\Core\Inputs\Types\Contracts
 */
interface Referencable {

	public function hasReference(): bool;

	public function display($value = null);

	public function getReference();

	public function getRoute() : ?string;

	public function getRouteParams() : array;

}