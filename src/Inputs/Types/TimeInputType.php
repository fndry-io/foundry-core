<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasDateFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;

/**
 * Class DateType
 *
 * @package Foundry\Requests\Types
 */
class TimeInputType extends DateTimeInputType {

	use HasMinMax;
	use HasDateFormat;

    protected $format = "H:i";

    public function __construct(string $name, string $label = null, bool $required = true, string $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder);
        $this->setInterval(['minutes' => 5]);
        $this->setMask("00:00");
        $this->setMaskFormat("HH:mm");
        $this->setDateFormat("HH:mm");
        $this->setMode("range");
        $this->setHelp(__('The time of the event in HH:mm'));
        $this->timeOnly();
    }

}
