<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasMinMax;


/**
 * Class TextType
 *
 * @package Foundry\Requests\Types
 */
class TelInputType extends InputType {

	use HasMinMax;

	//todo find a better alternative to this. New format is +15555555555 and should become +1 555 555 5555
//	protected $pattern = "/^1?(\d{3})(\d{3})(\d{4})$/";
//
//	protected $replacement = "($1)-$2-$3";

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
		$type = 'tel';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
		$this->addRule('telephone');
		$this->setCountryCode('1');
        $this->setMax( 16 )
            ->setPlaceholder('+1 ...')
            ->setHelp(__('An international number starting with a dialing code. E.G. +1 555 555 5555'));

	}

	public function setCountryCode($code)
	{
		$this->setAttribute('country', $code);
	}

	public function getCountryCode()
	{
		return $this->getAttribute('country');
	}

	public function display( $value = null ) {
		if ($value == null) {
			$value = $this->getValue();
		}
//		if ($value) {
//			$value = phone_number_format($value, $this->pattern, $this->replacement);
//		}
		return $value;
	}

}
