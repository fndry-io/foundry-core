<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Contracts\HasIdentity;
use Foundry\Core\Models\Contracts\HasNode;
use Foundry\Core\Models\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Trait Referencable
 *
 * @property Model $reference
 *
 * @package Foundry\Core\Models\Traits
 */
trait Referencable {

	/**
	 * @return MorphTo
	 */
	public function reference()
	{
		return $this->morphTo()->withoutGlobalScopes();
	}

    /**
     * @return Model
     */
	public function getReference()
    {
        return $this->reference;
    }

    public function setReference(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->attachReference($model);
    }

    /**
     * @param Model $model
     */
	public function attachReference(Model $model)
    {
        $this->reference()->associate($model);
        $this->setRelation('reference', $model);
        if ($model instanceof HasNode && $this instanceof HasNode) {
            $this->setNode($model->getNode());
        }
    }

    /**
     * Detach the reference
     */
    public function detachReference()
    {
        $this->reference()->dissociate();
    }

}
