<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\Traits\HasClass;
use Foundry\Core\Inputs\Types\Traits\HasForm;
use Foundry\Core\Inputs\Types\Traits\HasHelp;
use Foundry\Core\Inputs\Types\Traits\HasId;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasName;
use Foundry\Core\Inputs\Types\Traits\HasTitle;
use Foundry\Core\Inputs\Types\Traits\HasValue;

/**
 * Class LinkType
 *
 * @package Foundry\Requests\Types
 */
class LinkType extends BaseType implements Inputable {

	use HasId,
		HasLabel,
		HasClass,
		HasTitle,
        HasName,
        HasValue,
        HasHelp,
		HasForm
		;

	public function __construct(
		string $name,
		string $label,
		string $title = null,
		string $id = null
	) {
		parent::__construct();
		$this->setLabel( $label );
		$this->setName($name);
		$this->setTitle( $title );
		$this->setType( 'link' );
		$this->setId( $id );
	}

    public function display($value = null) {
        $value = $this->getValue();
        return $value;
    }
}
