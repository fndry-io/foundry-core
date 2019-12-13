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
            $column = $model->getCuidColumn();
            if (empty($model->{$column})) {
                $model->{$column} = Cuid::make();
            }
        } );
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getCuidColumn()
    {
        return defined('static::CUID') ? static::CUID : 'cuid';
    }

}
