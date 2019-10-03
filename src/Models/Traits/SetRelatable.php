<?php


namespace Foundry\Core\Models\Traits;


use Foundry\Core\Models\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait SetRelatable
{

    /**
     * @param Model|int $model The Model or primary to retrieve the model by
     * @param string $relation The relation it should set on the Model
     */
    public function setBelongsToAttribute($model, $relation)
    {
        $relation = $this->$relation();
        if (! $relation instanceof Relation) {
            if (is_null($relation)) {
                throw new \LogicException(sprintf(
                    '%s::%s must return a relationship instance, but "null" was returned. Was the "return" keyword used?', static::class, $relation
                ));
            }
            throw new \LogicException(sprintf(
                '%s::%s must return a relationship instance.', static::class, $relation
            ));
        }

        $class = $relation->getRelated();

        if (!is_object($model) && !empty($model)) {
            $model = $class::query()->findOrFail($model);
        }

        if ($model) {
            $relation->associate($model);
        } else {
            $relation->dissociate();
        }

    }
}
