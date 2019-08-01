<?php

namespace Foundry\Core\Inputs\Types;
use Foundry\Core\Inputs\Types\Traits\HasAction;


/**
 * Class FileType
 *
 * @package Foundry\Requests\Types
 */
class FileInputType extends InputType {

	use HasAction;

	public function __construct(
		string $name,
		string $label,
		bool $required = true,
		string $value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null,
        string $action = null
	) {
		$type = 'file';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
		$this->setAction($action);
	}

	public function setDeleteUrl($url){
		$this->setAttribute('deleteUrl', $url);
	}

}
