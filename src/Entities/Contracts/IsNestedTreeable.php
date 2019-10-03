<?php
namespace Foundry\Core\Entities\Contracts;

interface IsNestedTreeable {

	public function setParent($parent);

	public function getParent();

	public function getEntity();

	public function setEntity($entity);
}
