<?php

namespace Foundry\Core\Entities\Contracts;

interface IsFolder extends IsEntity, IsSoftDeletable
{

	public function isDirectory();

	public function isFile();

	public function file();

	public function getParent();

	public function parent();

}