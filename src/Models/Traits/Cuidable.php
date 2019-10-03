<?php

namespace Foundry\Core\Models\Traits;

use EndyJasmi\Cuid;

trait Cuidable
{
    /**
     * Boot function from laravel.
     */
    public static function bootCuidable() {
        static::creating( function ( $model ) {
            if (empty($model->{$model->getCuidName()})) {
                $model->{$model->getCuidName()} = Cuid::make();
            }
        } );
    }

    public function getCuidName()
    {
        if ($this->cuidKey) {
            return $this->cuidKey;
        } else {
            return 'cid';
        }
    }
}
