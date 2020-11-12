<?php

namespace Foundry\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

interface IsReferenceable {

	/**
	 * @return Model|object|null
	 */
	public function getReference();

	/**
	 * Remove the referenced object
	 *
	 * @return mixed
	 */
	public function detachReference();

	/**
	 * @param Model $reference
	 */
	public function attachReference( Model $reference);
}