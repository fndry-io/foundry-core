<?php
namespace Foundry\Core\Entities\Contracts;

interface IsNestedTreeable {

	//public function getRoot();

	public function setParent(IsNestedTreeable $parent = null);

	public function getParent();

	public function getEntity();

	public function setEntity($entity);
}