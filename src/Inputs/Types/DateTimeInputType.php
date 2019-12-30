<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Traits\HasDateTimeFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Illuminate\Support\Carbon;

/**
 * Class DateTimeType
 *
 * @package Foundry\Requests\Types
 * @method setMin($value = null, $add_rule = true) Set the minimum date. This should be a string in the DATE_ATOM format.
 * @method setMax($value = null, $add_rule = true) Set the maximum date. This should be a string in the DATE_ATOM format.
 */
class DateTimeInputType extends InputType implements Castable {

	use HasMinMax;
	use HasDateTimeFormat;

    /**
     * The date format to use
     *
     * Note: This uses DATE_ATOM as this is more consistent with the ISO 8601 standard
     *
     * @see https://www.php.net/manual/en/class.datetimeinterface.php#datetime.constants.types
     * @var string The date format
     */
	protected $format = DATE_ATOM;

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
        $this->addRule( 'date_format:' . $this->format );
		$this->setInterval(['minutes' => 5]);
		$this->setMask("0000-00-00 00:00");
		$this->setMaskFormat("YYYY-MM-DD HH:mm");
        $this->setDateFormat("YYYY-MM-DDTHH:mm:ssZZ");
        $this->setMode("calendar");
		$this->setHelp(__('Date/time in 24 hour format as yyyy-mm-dd hh:mm'));
	}

	static function cast()
	{
		return 'string';
	}

	public function getValue()
	{
		$value = parent::getValue();
		if ($value instanceof \DateTime) {
			$value = $value->format($this->format);
		}
		return $value;
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

    public function setMaskFormat($format)
    {
        $this->setAttribute('maskFormat', $format);
        return $this;
    }

    public function pickerOptions($options)
    {
        $this->setAttribute('pickerOptions', $options);
        return $this;
    }

	public function setInterval(array $interval)
    {
        $this->setAttribute('pickerOptions.interval', $interval);
        return $this;
    }

    public function setMode($mode)
    {
        $this->setAttribute('pickerOptions.mode', $mode);
        return $this;
    }

    public function timeOnly()
    {
        $this->setAttribute('pickerOptions.noTime', false);
        $this->setAttribute('pickerOptions.noDate', true);
        return $this;
    }

    public function dateOnly()
    {
        $this->setAttribute('pickerOptions.noTime', true);
        $this->setAttribute('pickerOptions.noDate', false);
        return $this;
    }

    public function setSelectableDays($days)
    {
        $this->setAttribute('pickerOptions.days', $days);
    }

}
