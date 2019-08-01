<?php

namespace Foundry\Core\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Foundry\Core\Entities\Contracts\IsArchiveable;
use Foundry\Core\Entities\Contracts\IsSoftDeletable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class EntityRepository
 *
 * An enhanced version of the Doctrine ORM EntityRepository adding in CRUD and Pagination
 *
 * @package Foundry\Core\Repositories
 */
abstract class EntityRepository extends \Doctrine\ORM\EntityRepository implements RepositoryInterface {

	abstract public function getAlias() : string;

	/**
	 *
	 * @param int $limit
	 *
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function all(\Closure $builder = null, int $limit = 20) : LengthAwarePaginator
	{
		$query = $this->createQueryBuilder($this->getAlias());
		if ($builder) {
			$query = $builder($query);
		} else {
			$query->select($this->getAlias());
		}
		$query->setFirstResult( 1 )->setMaxResults( $limit );

		return $query->getResult(Query::HYDRATE_OBJECT);
	}

	/**
	 * Returns a list of results
	 *
	 * @param \Closure $builder(QueryBuilder $query) The closure to send the Query Builder to
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return LengthAwarePaginator
	 */
	public function filter(\Closure $builder = null, int $page = 1, int $perPage = 20) : LengthAwarePaginator
	{
		$query = $this->_em->createQueryBuilder()->from($this->getEntityName(), $this->getAlias());
		if ($builder) {
			$query = $builder($query);
		} else {
			$query->select($this->getAlias());
		}
		return $this->paginate($query->getQuery(), $page, $perPage);
	}

	/**
	 * Get a Query to execute with
	 *
	 * @return QueryBuilder
	 */
	public function query()
	{
		return $this->_em->createQueryBuilder()->from($this->getEntityName(), $this->getAlias());
	}

	/**
	 * @param Query $query
	 * @param $page
	 * @param $perPage
	 *
	 * @return LengthAwarePaginator
	 */
	protected function paginate( $query, $page, $perPage ): LengthAwarePaginator
	{
		$offset = ( $page - 1 ) * $perPage;
		$query->setFirstResult( $offset )->setMaxResults( $perPage );

		$results = new Paginator( $query, $fetchJoinCollection = true );
		$results->setUseOutputWalkers( false );

		$items = [];
		foreach ( $results->getIterator() as $item ) {
			$items[] = $item;
		}

		$paginator = new \Illuminate\Pagination\LengthAwarePaginator( $items, count( $results ), $perPage, $page );
		$paginator->setPath(Request::fullUrl());

		return $paginator;
	}

	/**
	 * Create the entity and persist it to the database
	 *
	 * @param $entity
	 * @param bool $flush
	 */
	public function create( $entity, $flush = true ) {
		$this->save( $entity );
		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Update an entity and persist it to the database
	 *
	 * @param $entity
	 * @param bool $flush
	 */
	public function update( $entity, $flush = true ) {
		$this->save( $entity );
		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Save the entity to the database
	 *
	 * This will either insert or update the entity in the database
	 *
	 * @param $entity
	 * @param bool $flush
	 */
	public function save( $entity, $flush = true ) {
		$this->_em->persist( $entity );
		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Delete an entity in the database
	 *
	 * @param $entity
	 * @param bool $flush
	 */
	public function delete( $entity, $flush = true ) {
		if ($entity instanceof IsSoftDeletable) {
			if ($entity->isDeleted()) {
				$this->_em->remove( $entity );
			} else {
				$entity->setDeletedAt(new Carbon());
			}
		} else {
			$this->_em->remove( $entity );
		}

		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Restore an entity in the database
	 *
	 * @param $entity
	 * @param bool $flush
	 *
	 * @throws \Exception
	 */
	public function restore( $entity, $flush = true ) {

		if (!$entity instanceof IsSoftDeletable) {
			throw new \Exception(sprintf('Entity %s is not soft deletable', get_class($entity)));
		}

		if ($entity->isDeleted()) {
			$entity->restore();
		}

		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Archive an entity in the database
	 *
	 * @param $entity
	 * @param bool $flush
	 *
	 * @throws \Exception
	 */
	public function archive( $entity, $flush = true ) {
		if (!$entity instanceof IsArchiveable) {
			throw new \Exception(sprintf('Entity %s is not archiveable', get_class($entity)));
		}

		if ($entity->isArchived()) {
			$this->_em->remove( $entity );
		} else {
			$entity->setArchivedAt(new Carbon());
		}

		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * Un-Archive an entity in the database
	 *
	 * @param $entity
	 * @param bool $flush
	 *
	 * @throws \Exception
	 */
	public function unArchive( $entity, $flush = true ) {
		if (!$entity instanceof IsArchiveable) {
			throw new \Exception(sprintf('Entity %s is not archiveable', get_class($entity)));
		}

		if ($entity->isArchived()) {
			$entity->unArchive();
		}

		if ($flush) $this->_em->flush( $entity );
	}

	/**
	 * @param $results
	 *
	 * @return array
	 */
	public function labelListFromArray($results)
	{
		$list = [];
		foreach ($results as $result) {
			$list[] = [
				'value' => $result['id'],
				'text' => $result['label'],
			];
		}
		return $list;
	}

	/**
	 * Persist to memory
	 *
	 * @param null $entity
	 */
	public function persist( $entity = null )
	{
		$this->_em->persist( $entity );
	}

	/**
	 * Save to the database
	 *
	 * @param null $entity
	 */
	public function flush( $entity = null )
	{
		$this->_em->flush( $entity );
	}
}