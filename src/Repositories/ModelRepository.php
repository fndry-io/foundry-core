<?php

namespace Foundry\Core\Repositories;

use Foundry\Core\Models\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class ModelRepository
 *
 * @package Foundry\Core\Repositories
 */
abstract class ModelRepository implements RepositoryInterface {

	protected static $instance;

	/**
	 * @return string|Model
	 */
	abstract public function getClassName();

	/**
	 * @return ModelRepository|self|static
	 */
	public static function repository()
	{
		$class = get_called_class();
		if (!isset(self::$instance[$class])) {
			self::$instance[$class] = new $class();
		}
		return self::$instance[$class];
	}

	/**
	 * Get a Query to execute with
	 *
	 * @return Builder
	 */
	public function query()
	{
		return $this->getClassName()::query();
	}

	/**
	 * Finds an object by its primary key / identifier.
	 *
	 * @param mixed $id The identifier.
	 *
	 * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|object
	 */
	public function find($id)
	{
		return $this->query()->find($id);
	}

	/**
	 * Finds all objects in the repository.
	 *
	 * @return Collection|Model[] The objects.
	 */
	public function findAll()
	{
		return $this->query()->get();
	}

	/**
	 * Finds objects by a set of criteria.
	 *
	 * Optionally sorting and limiting details can be passed. An implementation may throw
	 * an UnexpectedValueException if certain values of the sorting or limiting details are
	 * not supported.
	 *
	 * @param mixed[]       $criteria
	 * @param string[]|null $orderBy
	 * @param int|null      $limit
	 * @param int|null      $offset
	 *
	 * @return Collection|Model[] The objects.
	 *
	 */
	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
	{
		$query = $this->query();
		foreach ($criteria as $key => $value) {
			$query->where($key, $value);
		}

		if ($orderBy) {
			$query->orderBy($orderBy);
		}

		if ($limit) {
			$query->limit($limit);
		}

		if ($offset) {
			$query->offset($offset);
		}

		return $query->get();
	}

	/**
	 * Finds a single object by a set of criteria.
	 *
	 * @param mixed[] $criteria The criteria.
	 *
	 * @return object|null The object.
	 */
	public function findOneBy(array $criteria)
	{
		$query = $this->query();
		foreach ($criteria as $key => $value) {
			$query->where($key, $value);
		}
		return $query->first();
	}

	/**
	 * Get the count of records
	 *
	 * @param array $criteria
	 *
	 * @return int
	 */
	public function count(array $criteria)
	{
		$query = $this->query();
		foreach ($criteria as $key => $value) {
			$query->where($key, $value);
		}
		return $query->count();
	}

	/**
	 *
	 * @param \Closure|null $builder
	 * @param int $limit
	 *
	 * @return LengthAwarePaginator
	 */
	public function all(\Closure $builder = null, int $limit = 20) : LengthAwarePaginator
	{
		$query = $this->query();
		if ($builder) {
			$query = $builder($query);
		} else {
			$query->select(['*']);
		}
		$query->limit($limit);

		return $query->get();
	}

	/**
	 * Returns a list of results
	 *
	 * @param \Closure $builder(QueryBuilder $query) The closure to send the Query Builder to
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return Paginator
	 */
	public function filter(\Closure $builder = null, int $page = 1, int $perPage = 20) : Paginator
	{
		$query = $this->query();
		if ($builder) {
			$query = $builder($query);
		} else {
			$query->select(['*']);
		}
		return $this->paginate($query, $page, $perPage);
	}


	/**
	 * @param $query
	 * @param $page
	 * @param $perPage
	 * @param $pageName
	 *
	 * @return Paginator
	 */
	protected function paginate( Builder $query, $page, $perPage, $pageName = 'page' ): Paginator
	{
		return $query->paginate($perPage, null, $pageName, $page);
//
//
//		$page = $page ?: \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);
//
//		$total = $query->getCountForPagination();
//
//		$results = $total ? $query->forPage($page, $perPage)->get() : collect();
//
//		return Container::getInstance()->makeWith(\Illuminate\Pagination\LengthAwarePaginator::class, [
//			'items' => $results,
//			'total' => $total,
//			'perPage' => $perPage,
//			'currentPage' => $page,
//			'options' => [
//				'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath($pageName),
//				'pageName' => $pageName,
//			]
//		]);
	}

	/**
	 * Create the entity and persist it to the database
	 *
	 * @param Model $model
	 *
	 * @return mixed
	 */
	public function create( $model ) {
		return $this->save( $model );
	}

	/**
	 * Update an entity and persist it to the database
	 *
	 * @param Model $model
	 *
	 * @return mixed
	 */
	public function update( $model ) {
		return $this->save( $model );
	}

	/**
	 * Save the entity to the database
	 *
	 * This will either insert or update the entity in the database
	 *
	 * @param Model $model
	 *
	 * @return mixed
	 */
	public function save( $model ) {
		return $model->save();
	}

	/**
	 * Delete an record in the database
	 *
	 * @param Model|int $model
	 *
	 * @return bool|mixed|null
	 * @throws \Exception
	 */
	public function delete( $model ) {
		if (is_int($model)) {
			$model = $this->find($model);
		}
		return $model->delete();
	}

	/**
	 * Restore an entity in the database
	 *
	 * @param SoftDeletes $model
	 *
	 * @return mixed
	 */
	public function restore( $model ) {
		return $model->restore();
	}

	/**
	 * @param $results
	 * @param string $textKey
	 * @param string $valueKey
	 *
	 * @return array
	 */
	public function labelListFromArray($results, $textKey = 'label', $valueKey = 'id')
	{
		$list = [];
		foreach ($results as $result) {
			$list[] = [
				'value' => Arr::get($result, $valueKey),
				'text' => Arr::get($result, $textKey),
			];
		}
		return $list;
	}

}