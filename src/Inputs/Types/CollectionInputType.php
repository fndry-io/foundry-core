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

    /**
     * Sets the the collection/form schema to use for rendering this form in the front end
     *
     * @param Inputs $inputs
     * @return CollectionInputType
     */
	public function setCollection(Inputs $inputs): CollectionInputType
    {
        $this->collection = $inputs;
        return $this;
    }

    public function getCollection(): Inputs
    {
        return $this->collection;
    }

    /**
     * Sets the no items found text when no items have been added
     *
     * @param string $text
     * @return CollectionInputType
     */
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
     * Sets the legend used on each form fieldset
     *
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
