<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Traits\HasDateFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasTimeFormat;
use Illuminate\Support\Carbon;

/**
 * Class DateTimeType
 *
 * @package Foundry\Requests\Types
 */
class DateTimeInputType extends InputType implements Castable {

	use HasMinMax;
	use HasDateFormat;
	use HasTimeFormat;

	protected $format = "Y-m-d\TH:i:sO";

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
		$this->setDateFormat("Y-m-d");
		$this->setTimeFormat("H:i");
		$this->setAttribute('stepping', 5);
	}

	static function cast()
	{
		return 'datetime';
	}

	public function getValue()
	{
		$value = parent::getValue();
		if ($value instanceof \DateTime) {
			$value = $value->format($this->format);
		}
		return $value;
	}

	public function jsonSerialize(): array {
		$json = parent::jsonSerialize();
		$json['value'] = $this->getValue();
		//convert the format to a moment.js structure
		$json['dateFormat'] = convert_to_moment_js($this->getDateFormat());
		$json['timeFormat'] = convert_to_moment_js($this->getTimeFormat());
		return $json;
	}

	public function getCastValue( $value ) {

		if ($value) {
			if ($value instanceof \DateTime) {
				return $value;
			} else if (is_string($value) && $date = Carbon::parse($value)) {
				return $date;
			} else if (is_array($value) && isset($value['date'])) {
				return Carbon::__set_state($value);
			}
		}
		return null;
	}
}
