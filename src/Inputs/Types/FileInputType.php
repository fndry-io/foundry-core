<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Entities\Contracts\IsFolder;
use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasAction;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;
use Foundry\Core\Inputs\Types\Traits\HasParams;

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
	 * @var IsFolder|null
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

	public function setFolder(IsFolder $folder){
		$this->folder = $folder;
		$this->setParams(['folder' => $folder->getKey()]);
		return $this;
	}

	public function setLayout($layout)
    {
        $this->setAttribute('layout', $layout);
        return $this;
    }

    public function getLayout()
    {
        return $this->getAttribute('layout');
    }

    /**
     * @return IsFolder|null
     */
    public function getFolder(): ?IsFolder
    {
        return $this->folder;
    }

    public function setFiles(array $files)
    {
        $this->setAttribute('files', $files);
    }

    public function withFileDetails()
    {
        $this->setAttribute('show_file_details', true);
        return $this;
    }

    public function withoutFileDetails()
    {
        $this->setAttribute('show_file_details', false);
        return $this;
    }

}
