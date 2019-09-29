<?php

namespace Foundry\Core\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait SoftDeleteable
{

	/**
	 * Restore an entity in the database
	 *
	 * @param Model|int $id
	 *
	 * @return mixed
	 */
	public function restore($id)
	{
		if ($id instanceof Model) {
			$model = $id;
		} else {
			$model = $this->find($id);
		}

		return $model->restore();
	}
}