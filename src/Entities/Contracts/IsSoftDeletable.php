<?php

namespace Foundry\Core\Entities\Contracts;

interface IsSoftDeletable {

	/**
	 * @return \DateTime
	 */
	public function getDeletedAt();

	/**
	 * @param \DateTime|null $deleted_at
	 */
	public function setDeletedAt(\DateTime $deleted_at = null);

	/**
	 * Restore the soft-deleted state
	 */
	public function restore();

	/**
	 * @return bool
	 */
	public function isDeleted();
}