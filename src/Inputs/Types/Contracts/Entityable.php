<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Entityable {

	public function setEntity( Arrayable $entity = null );

	public function getEntity();

	public function attachInputs( Inputable ...$input_types );
}