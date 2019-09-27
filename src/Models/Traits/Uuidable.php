<?php

namespace Foundry\Core\Models\Traits;


use Ramsey\Uuid\Uuid;

trait Uuidable {

	/**
	 * Boot function from laravel.
	 */
	public static function bootUuidable() {
		static::creating( function ( $model ) {
			if (empty($model->{$model->getUuidName()})) {
				$model->{$model->getUuidName()} = Uuid::uuid4();
			}
		} );
	}

	public function getUuidName()
	{
		if ($this->uuidKey) {
			return $this->uuidKey;
		} else {
			return 'uuid';
		}
	}
}
