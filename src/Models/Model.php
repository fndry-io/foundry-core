<?php

namespace Foundry\Core\Models;

use Foundry\Core\Entities\Contracts\HasIdentity;
use Foundry\Core\Entities\Contracts\HasVisibility;
use Foundry\Core\Models\Traits\SetRelatable;
use Foundry\Core\Models\Traits\Visible;
use Illuminate\Contracts\Support\Arrayable;

class Model extends \Illuminate\Database\Eloquent\Model implements HasVisibility, Arrayable, HasIdentity
{
	use Visible;
	use SetRelatable;
}
