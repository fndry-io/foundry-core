<?php

namespace Foundry\Core\Entities\Traits;

/**
 * Trait Addressable
 *
 * @package Foundry\Core\Entities\Traits
 *
 * @property string $street
 * @property string $city
 * @property string $region
 * @property string $country
 * @property string $code
 */
trait Addressable
{
	protected $type;
	protected $street;
	protected $city;
	protected $region;
	protected $country;
	protected $code;
}
