<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Referencable {

	public function hasReference(): bool;

	public function display($value = null);

	public function getReference();

	public function getModel(): Model;

	public function hasModel(): bool;

	public function getRoute() : ?string;

	public function getRouteParams() : array;

}