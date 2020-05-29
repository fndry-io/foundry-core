<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Castable;
use Foundry\Core\Inputs\Types\Traits\HasDateTimeFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Illuminate\Support\Carbon;

/**
 * Class DateTimeType
 *
 * Because DateTimes have been a difficult concept to get right, the following is the correct understanding to ensure
 * DateTime values remain consistent:
 *
 * - ALWAYS save the datetime in UTC timezone +0000
 * - ALWAYS ensure the value coming in as a timezone on it
 * - ALWAYS normalise the input value to UTC timezone
 *
 * DateTime values on the server must ALWAYS be in UTC timezone.
 *
 * What is DISPLAYED to the user can be the datetime in their timezone.
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
	protected $format = "Y-m-d\TH:i:s+0000";

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
        $this->addRule( 'valid_date:' . $this->format );
		$this->setInterval(['minutes' => 1]);
		$this->setMask("0000-00-00 00:00");
		$this->setMaskFormat("YYYY-MM-DD HH:mm");
        $this->setDateFormat("YYYY-MM-DDTHH:mm:ssZZ");
        $this->setMode("calendar");
		$this->setHelp(__('Date/time in 24 hour format as yyyy-mm-dd hh:mm'));
		$this->disabledAutoUpdate();
	}

	static function cast()
	{
		return 'string';
	}

	public function getFormat()
    {
        return $this->format;
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
				return $date->utc();
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

    /**
     * Allows you to set the type of picker displayed
     *
     * @param string $mode Either "calendar" for a calendar or "range" for a simple clock style date
     * @return $this
     */
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
        return $this;
    }

    public function setMaxDate($date)
    {
        $this->setAttribute('pickerOptions.maxDate', $date);
        return $this;
    }

    public function setMinDate($date)
    {
        $this->setAttribute('pickerOptions.minDate', $date);
        return $this;
    }

    public function setValidDates($dates)
    {
        $this->setAttribute('pickerOptions.validDates', $dates);
        return $this;
    }

    public function disabledAutoUpdate()
    {
        $this->setAttribute('pickerOptions.autoUpdate', false);
        $this->setAttribute('pickerOptions.noButtons', false);
        return $this;
    }

    public function enableAutoUpdate()
    {
        $this->setAttribute('pickerOptions.autoUpdate', true);
        $this->setAttribute('pickerOptions.noButtons', true);
        return $this;
    }

}
