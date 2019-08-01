<?php

namespace Foundry\Core\Entities\Traits;

/**
 * Blameable Trait
 *
 */
interface IsBlameable
{
	/**
	 * Sets createdBy.
	 *
	 * @param  string $created_by
	 * @return $this
	 */
	public function setCreatedBy($created_by);

	/**
	 * Returns created_by.
	 *
	 * @return string
	 */
	public function getCreatedBy();

	/**
	 * Sets $updated_by.
	 *
	 * @param  string $updated_by
	 * @return $this
	 */
	public function setUpdatedBy($updated_by);

	/**
	 * Returns updatedBy.
	 *
	 * @return string
	 */
	public function getUpdatedBy();
}
