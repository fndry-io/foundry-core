<?php

namespace Foundry\Core\Entities\Contracts;

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
	 * Sets $updated_by.
	 *
	 * @param  string $updated_by
	 * @return $this
	 */
	public function setUpdatedBy($updated_by);

    /**
     * @return mixed
     */
	public function isCreatedBy($user);

    /**
     * @return mixed
     */
    public function isUpdatedBy($user);

}
