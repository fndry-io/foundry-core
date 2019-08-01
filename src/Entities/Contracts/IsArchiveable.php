<?php

namespace Foundry\Core\Entities\Contracts;

interface IsArchiveable {

	/**
	 * @return \DateTime
	 */
	public function getArchivedAt();

	/**
	 * @param \DateTime|null $archived_at
	 */
	public function setArchivedAt($archived_at);

	/**
	 * Restore the archived state
	 */
	public function unArchive();

	/**
	 * @return bool
	 */
	public function isArchived();
}