<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasDateFormat;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;

/**
 * Class DateType
 *
 * @package Foundry\Requests\Types
 */
class DateInputType extends DateTimeInputType {

	use HasMinMax;
	use HasDateFormat;

	protected $format = "Y-m-d";

    public function __construct(string $name, string $label = null, bool $required = true, string $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder);
        $this->setMask("0000-00-00");
        $this->setMaskFormat("YYYY-MM-DD");
        $this->setDateFormat("YYYY-MM-DD");
        $this->setMode("calendar");
        $this->setHelp(__('Date as yyyy-mm-dd'));
        $this->dateOnly();
    }

}
