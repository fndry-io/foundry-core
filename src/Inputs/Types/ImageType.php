<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasForm;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasValue;

/**
 * Class ImageType
 *
 * @package Foundry\Requests\Types
 */
class ImageType extends BaseType implements Inputable {

	use HasId,
		HasLabel,
		HasClass,
        HasName,
        HasValue,
        HasHelp,
		HasForm
	;

	public function __construct(
		string $name,
		string $label,
		string $alt = null,
		string $id = null
	) {
		parent::__construct();
		$this->setLabel( $label );
		$this->setName($name);
		$this->setAlt($alt);
		$this->setType( 'image' );
		$this->setId( $id );
	}

	public function getAlt(){
	    return $this->getAttribute('alt');
    }

    /**
     * @param $alt
     *
     * @return ImageType
     */
    public function setAlt($alt): ImageType{
	    $this->setAttribute('alt', $alt);
	    return $this;
    }

    public function display($value = null) {
        $value = $this->getValue();
        return $value;
    }
}
