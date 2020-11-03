<?php

namespace Foundry\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

interface IsFolder extends IsSoftDeletable
{
	public function isDirectory();

	public function isFile();

	public function file();

	public function getParent();

	public function parent();

	public function setFile(IsFile $file);

	public function setReference(Model $entity);

}
