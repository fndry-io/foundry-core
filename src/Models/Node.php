<?php

namespace Foundry\Core\Models;

use Foundry\Core\Entities\Contracts\IsNestedTreeable;
use Foundry\Core\Models\Traits\Uuidable;
use Foundry\System\Entities\Contracts\IsNode;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class Node
 *
 * @package Foundry\Core\Models
 */
class Node extends Model implements IsNode, IsNestedTreeable {

	use Uuidable;
	use NodeTrait;

	protected $fillable = [];

	protected $visible = [
		'id',
		'uuid'
	];

	/**
	 * The left column name
	 *
	 * @return string
	 */
	public function getLftName()
	{
		return 'lft';
	}

	/**
	 * The right column name
	 *
	 * @return string
	 */
	public function getRgtName()
	{
		return 'rgt';
	}

	/**
	 * Get the Parent Id column name
	 *
	 * @return string
	 */
	public function getParentIdName()
	{
		return 'parent_id';
	}

	/**
	 * Set the parent id attribute by parent
	 *
	 * @param integer $value
	 *
	 * @throws \Exception
	 */
	public function setParentAttribute($value)
	{
		$this->setParentIdAttribute($value);
	}

	/**
	 * The entity for this Node
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function entity()
	{
		return $this->morphTo();
	}

	/**
	 * @param IsNestedTreeable|Model|null $parent
	 */
	public function setParent(IsNestedTreeable $parent = null)
	{
		$this->setParent($parent->getKey());
	}

	/**
	 * The parent Node
	 *
	 * @return mixed
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Get the associated entity
	 *
	 * @return mixed
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * Set the entity for this Node
	 *
	 * @param $entity
	 */
	public function setEntity($entity)
	{
		$this->entity()->associate($entity);
	}
}