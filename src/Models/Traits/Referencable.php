<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Entities\Contracts\HasNode;
use Foundry\Core\Models\Model;

trait Referencable {

	/**
	 * @return Model|object|null
	 */
	public function reference()
	{
		return $this->morphTo();
	}

	public function setReference($model)
	{
		$this->reference()->associate($model);
		if ($model instanceof HasNode && $this instanceof HasNode) {
			$this->setNode($model->getNode());
		}
	}
	
}