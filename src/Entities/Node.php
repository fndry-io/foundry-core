<?php
namespace Foundry\Core\Entities;

use Foundry\Core\Entities\Contracts\HasIdentity;
use Foundry\Core\Entities\Contracts\IsNestedTreeable;
use Foundry\Core\Entities\Traits\Identifiable;
use Foundry\Core\Entities\Traits\NestedTreeable;
use Foundry\Core\Entities\Traits\Uuidable;
use LaravelDoctrine\ORM\Facades\EntityManager;

/**
 * Class Node
 *
 * @package Foundry\Core\Entities
 */
class Node extends Entity implements HasIdentity, IsNestedTreeable {

	use Identifiable;
	use Uuidable;
	use NestedTreeable;

    /**
     * @var string
     */
    protected $entity_type;

    /**
     * @var integer
     */
    protected $entity_id;

	protected $fillable = [];

	protected $visible = [
		'id',
		'uuid'
	];

	public function __construct( array $properties = [] ) {
		parent::__construct( $properties );
		$this->setUuid();
	}

	public function getLeft()
	{
		return $this->lft;
	}

	public function getRight()
	{
		return $this->rgt;
	}

    /**
     * @return int
     */
    public function getEntityId(): int {
        return $this->entity_id;
    }

    /**
     * @return string
     */
    public function getEntityType(): string {
        return $this->entity_type;
    }

    /**
     * @return Entity|object|null
     */
    public function getEntity()
    {
        if ($this->entity_id && $this->entity_type) {
            return EntityManager::getRepository($this->getEntityType())->find($this->getEntityId());
        } else {
            return null;
        }
    }

    /**
     * @param int $entity_id
     */
    public function setEntityId( int $entity_id ): void {
        $this->entity_id = $entity_id;
    }

    /**
     * @param string $entity_type
     */
    public function setEntityType( string $entity_type ): void {
        $this->entity_type = $entity_type;
    }

    /**
     * Attach a entity to the entity
     *
     * @param HasIdentity $entity
     */
    public function attachEntity( HasIdentity $entity)
    {
        $this->setEntityType(get_entity_class($entity));
        $this->setEntityId($entity->getId());
    }

    /**
     * Detach the entity
     */
    public function detachEntity() {
        $this->setEntityId(null);
        $this->setEntityType(null);
    }

}