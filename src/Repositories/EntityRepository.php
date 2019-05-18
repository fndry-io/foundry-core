<?php

namespace Foundry\Core\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

/**
 * Class EntityRepository
 *
 * An enhanced version of the Doctrine ORM EntityRepository adding in CRUD and Pagination
 *
 * @package Foundry\Core\Repositories
 */
abstract class EntityRepository extends \Doctrine\ORM\EntityRepository implements RepositoryInterface {

	use PaginatesFromRequest;

	abstract public function getAlias() : string;

	/**
	 * @param int $limit
	 * @param int $page
	 *
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function all(int $limit = 20, int $page = 1) : LengthAwarePaginator
	{
		return $this->paginateAll($limit, $page);
	}

	/**
	 * Returns a list of results
	 *
	 * @param \Closure $builder(QueryBuilder $query) The closure to send the Query Builder to
	 * @param int $perPage
	 * @param string $pageName
	 *
	 * @return LengthAwarePaginator
	 */
	public function filter(\Closure $builder = null, int $perPage = 20, $pageName = 'page') : LengthAwarePaginator
	{
		$query = $this->createQueryBuilder($this->getAlias());
		if ($builder) {
			$query = $builder($query);
		}
		return $this->paginate($query->getQuery(), $perPage, $pageName);
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