<?php

namespace Foundry\Core\Models;

use Foundry\Core\Models\Traits\SetRelatable;
use Foundry\Core\Models\Traits\Visible;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
	use Visible;
	use SetRelatable;
}
