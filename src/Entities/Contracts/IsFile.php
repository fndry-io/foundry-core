<?php

namespace Foundry\Core\Entities\Contracts;

/**
 * Interface IsFile
 *
 * @package Foundry\Core\Entities\Contracts
 */
interface IsFile extends IsEntity, IsSoftDeletable
{

	public function isPublic();

	public function folder();
}