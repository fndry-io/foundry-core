<?php

namespace Foundry\Core\Models\Contracts;

/**
 * Interface IsFile
 *
 * @package Foundry\Core\Models\Contracts
 */
interface IsFile extends IsSoftDeletable
{

	public function isPublic();

	public function folder();
}
