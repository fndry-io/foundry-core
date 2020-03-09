<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\InputCollection;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;

class CollectionType extends FormType {

	use HasMinMax,
		HasMultiple,
        HasLabel
    ;

    /**
     * @var null|InputCollection
     */
	protected $collection;

    /**
     * CollectionType constructor.
     * @param InputCollection $collection
     * @param null $name
     * @param null $id
     */
	public function __construct( InputCollection $collection, $name = null, $id = null ) {
		parent::__construct($name, $id);
		$this->setCollection($collection);
	}

    /**
     * @param InputCollection|null $collection
     * @return $this
     */
    public function setCollection(?InputCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return InputCollection|null
     */
    public function getCollection(): ?InputCollection
    {
        return $this->collection;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();

        if (!empty($this->collection)) {
            $json['collection'] = [];
            foreach ($this->collection as $child) {
                $json['collection'][] = $child->toArray();
            }
        }

        return $json;
    }

}
