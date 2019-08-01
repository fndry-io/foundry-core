<?php

namespace Foundry\Core\Inputs\Types;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasAction;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;
use Foundry\Core\Inputs\Types\Traits\HasParams;
use Foundry\System\Entities\Folder;


/**
 * Class FileType
 *
 * @package Foundry\Requests\Types
 */
class FileInputType extends InputType implements IsMultiple {

	use HasAction;
	use HasParams;
	use HasMinMax;
	use HasMultiple;

	/**
	 * @var Folder|null
	 */
	protected $folder;

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
		return $this;
	}

	public function setFolder(Folder $folder){
		$this->folder = $folder;
		$this->setParams(['folder' => $folder->getId()]);
		return $this;
	}

}
