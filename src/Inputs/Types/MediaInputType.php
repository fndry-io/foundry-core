<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Contracts\IsMultiple;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;
use Symfony\Component\Mime\MimeTypes;

/**
 * Class FileType
 *
 * @package Foundry\Requests\Types
 */
class MediaInputType extends InputType implements IsMultiple {

	use HasMinMax;
	use HasMultiple;

	protected $cast = "array";

	public function __construct(
		string $name,
		string $label,
		bool $required = true,
		string $value = null,
		string $position = 'full',
		string $rules = null,
		string $id = null,
		string $placeholder = null
	) {
		$type = 'media';
		parent::__construct( $name, $label, $required, $value, $position, $rules, $id, $placeholder, $type );
        $this->setAttribute('mimeTypes', []);
	}

	public function setMimeTypes(array $types = [])
    {
        $this->setAttribute('mimeTypes', $types);
        return $this;
    }

    public function addMimeTypes(array $types)
    {
        $this->setAttribute('mimeTypes', array_merge($this->getMimeTypes(), $types));
        return $this;
    }

    public function getMimeTypes()
    {
        return $this->getAttribute('mimeTypes');
    }

    public function addImagesTypes()
    {
        $this->addMimeTypes([
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg'
        ]);
        return $this;
    }

    public function getAllowedExtensions()
    {
        $extensions = [];
        $mimeTypes = new MimeTypes();

        foreach($this->getMimeTypes() as $type) {
            $extensions = [
                ...$extensions,
                ...$mimeTypes->getExtensions($type)
            ];
        }
        return $extensions;
    }

}
