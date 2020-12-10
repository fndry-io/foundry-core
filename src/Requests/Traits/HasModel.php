<?php

namespace Foundry\Core\Requests\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasModel {

	/**
	 * @var null|Model
	 */
	protected $model = null;

	public function getModel() {
		return $this->model;
	}

	public function setModel(Model $model) {
		$this->model = $model;
	}
}
