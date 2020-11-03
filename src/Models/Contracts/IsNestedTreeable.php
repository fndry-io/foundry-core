<?php
namespace Foundry\Core\Models\Contracts;

interface IsNestedTreeable {

	public function setParent($parent);

	public function getParent();

	public function getEntity();

	public function setEntity($entity);
}
