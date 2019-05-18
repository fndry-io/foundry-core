<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Models\InputCollection;

interface ColumnInterface {

	public static function columns(): InputCollection;
}