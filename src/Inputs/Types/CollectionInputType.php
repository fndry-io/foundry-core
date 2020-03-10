<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\InputCollection;
use Foundry\Core\Inputs\Types\Traits\HasLabel;
use Foundry\Core\Inputs\Types\Traits\HasMinMax;
use Foundry\Core\Inputs\Types\Traits\HasMultiple;

class CollectionInputType extends InputType {

	use HasMinMax,
		HasMultiple,
        HasLabel
    ;

	protected $cast = 'array';

    /**
     * @var null|InputCollection
     */
	protected $collection;

    /**
     * CollectionType constructor.
     * @param InputCollection $collection
     * @param null $name
     * @param null $label
     * @param bool $required
     */
	public function __construct( InputCollection $collection, $name = null, $label = null, $required = false) {
		parent::__construct($name, $label, $required);
		$this->setType('collection');
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

    /**
     * @param string $noContentText
     */
    public function setNoContentText(string $noContentText): void
    {
        $this->setAttribute('noContentText', $noContentText);
    }

    /**
     * @return string
     */
    public function getNoContentText(): string
    {
        return $this->attributes['noContentText'];
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        if (!empty($this->collection)) {
            $json['form'] = $this->collection->view();
        }
        return $json;
    }


}
