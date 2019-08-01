<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasDateFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;

/**
 * Class DateTimeType
 *
 * @package Foundry\Requests\Types
 */
class DateTimeInputType extends InputType {

	use HasMinMax;
	use HasDateFormat;

	protected $format = "Y-m-d H:i:s";

	public function __construct(
		string $name,
		string $label = null,
		bool $required = true,
		string $value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$type = 'datetime';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
		$this->addRule( 'date' );
	}

	static function cast()
	{
		return 'datetime';
	}

	public function jsonSerialize(): array {
		$json = parent::jsonSerialize(); // TODO: Change the autogenerated stub
		//convert the format to a moment.js structure
		$json['format'] = convert_to_moment_js($this->getFormat());
		return $json;

	}

}
