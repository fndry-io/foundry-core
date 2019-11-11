<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasDateFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Illuminate\Support\Carbon;

/**
 * Class DateType
 *
 * @package Foundry\Requests\Types
 */
class DateInputType extends InputType {

	use HasMinMax;
	use HasDateFormat;

	protected $format = "Y-m-d";

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
		$type = 'date';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
		$this->addRule( 'date_format:' . $this->format );
        $this->setDateFormat("Y-m-d");
    }

    static function cast()
    {
        return 'string';
    }

    public function setValue($value)
    {
        if ($value && $value instanceof Carbon) {
            $value = $value->format($this->getDateFormat());
        }
        return parent::setValue($value);
    }

    public function jsonSerialize(): array {
        $json = parent::jsonSerialize();
        //we do this as the value on the model is a full carbon date and we need the value to only be based on the
        //inputs format
        $this->setValue($this->getValue());
        $json['value'] = $this->getValue();
        //convert the format to a moment.js structure
        $json['dateFormat'] = convert_to_moment_js($this->getDateFormat());
        return $json;
    }

}
