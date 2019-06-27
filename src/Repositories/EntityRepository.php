<?php

namespace Foundry\Core\Repositories;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

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
	 */
	public function create( $entity ) {
		$this->save( $entity );
	}

	/**
	 * Update an entity and persist it to the database
	 *
	 * @param $entity
	 */
	public function update( $entity ) {
		$this->save( $entity );
	}

	/**
	 * Save the entity to the database
	 *
	 * This will either insert or update the entity in the database
	 *
	 * @param $entity
	 */
	public function save( $entity ) {
		$this->_em->persist( $entity );
		$this->_em->flush( $entity );
	}

	/**
	 * Delete an entity in the database
	 *
	 * @param $entity
	 */
	public function delete( $entity ) {
		$this->_em->remove( $entity );
		$this->_em->flush( $entity );
	}
}