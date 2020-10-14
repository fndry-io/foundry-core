<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;

/**
 * Class CollectionInputType
 *
 * Allows us to display a form to the user for completion
 *
 * @package Foundry\Core\Inputs\Types
 */
class CollectionInputType extends InputType {

	use HasMinMax,
		HasMultiple,
        HasLabel
    ;

	protected $cast = 'array';

	protected Inputs $collection;

    /**
     * FormInputType constructor.
     * @param Inputs $inputs
     * @param null $name
     * @param null $label
     * @param bool $required
     */
	public function __construct(Inputs $inputs, $name = null, $label = null, $required = false) {
		parent::__construct($name, $label, $required);
		$this->setType('collection');
		$this->setCollection($inputs);
	}

	public function setCollection(Inputs $inputs): CollectionInputType
    {
        $this->collection = $inputs;
        return $this;
    }

    public function getCollection(): Inputs
    {
        return $this->collection;
    }

    public function setNoContentText(string $text): CollectionInputType
    {
        $this->setAttribute('no_content_text', $text);
        return $this;
    }

    /**
     * @return string
     */
    public function getNoContentText(): string
    {
        return $this->attributes['no_content_text'];
    }

    /**
     * @return mixed
     */
    public function getItemLabel()
    {
        return $this->getAttribute('item_label');
    }

    /**
     * @param string $item_label
     * @return CollectionInputType
     */
    public function setItemLabel($item_label): CollectionInputType
    {
        $this->setAttribute('item_label', $item_label);
        return $this;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        if (!empty($this->inputs)) {
            $json['schema'] = $this->collection->view(app('request'));
        }
        return $json;
    }


}
