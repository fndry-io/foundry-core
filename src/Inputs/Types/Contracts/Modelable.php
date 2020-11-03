<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Modelable {

	public function setModel( Model &$model = null );

	public function getModel(): Model;

	public function attachInputs( Inputable ...$input_types );
}
