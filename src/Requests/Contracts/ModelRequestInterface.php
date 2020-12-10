<?php

namespace Foundry\Core\Requests\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ModelRequestInterface {

	/**
	 * @return Model|null
	 */
	public function getModel();

	/**
	 * @param Model $model
	 *
	 * @return mixed
	 */
    /**
     * @param Model $model
     * @return mixed
     */
	public function setModel($model);

	/**
	 * Find the Model for the request
	 *
	 * @param $id
	 *
	 * @return Model|null
	 */
	public function findModel($id);

}
