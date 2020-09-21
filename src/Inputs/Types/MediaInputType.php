<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;

/**
 * Class FileType
 *
 * @package Foundry\Requests\Types
 */
class MediaInputType extends InputType implements IsMultiple {

	use HasMinMax;
	use HasMultiple;

	protected $cast = "array";

	public function __construct(
		string $name,
		string $label,
		bool $required = true,
		string $value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$type = 'media';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
	}

}
