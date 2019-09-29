<?php

namespace Foundry\Core\Repositories;

use Foundry\Core\Models\Model;
use Foundry\Core\Entities\Contracts\IsEntity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ModelRepository
 *
 * @package Foundry\Core\Repositories
 */
abstract class ModelRepository implements RepositoryInterface
{

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
		if ( ! isset(self::$instance[$class])) {
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
	 * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|object|IsEntity|Model
	 */
	public function find($id)
	{
		return $this->query()->find($id);
	}

	/**
	 * Find the record or throws an exception
	 *
	 * @param int|Model $id
	 *
	 * @return Model|IsEntity|Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|object
	 */
	protected function getModel($id)
	{
		if ($id instanceof \Illuminate\Database\Eloquent\Model) {
			return $id;
		} else if (is_int($id)) {
			return $this->query()->findOrFail($id);
		} else {
			throw new \Exception('Invalid id value passed to findOrAbort');
		}
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
	 * @param mixed[] $criteria
	 * @param string[]|null $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 *
	 * @return Collection|Model[] The objects.
	 *
	 */
	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
	{
		$query = $this->query();
		foreach ($criteria as $key => $value) {
			if (is_array($value)) {
				$query->whereIn($key, $value);
			} else {
				$query->where($key, $value);
			}
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
			if (is_array($value)) {
				$query->whereIn($key, $value);
			} else {
				$query->where($key, $value);
			}
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
			if (is_array($value)) {
				$query->whereIn($key, $value);
			} else {
				$query->where($key, $value);
			}
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
	public function all(\Closure $builder = null, int $limit = 20): LengthAwarePaginator
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
	 * @param \Closure $builder (QueryBuilder $query) The closure to send the Query Builder to
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return Paginator
	 */
	public function filter(\Closure $builder = null, int $page = 1, int $perPage = 20): Paginator
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
	protected function paginate(Builder $query, $page, $perPage, $pageName = 'page'): Paginator
	{
		return $query->paginate($perPage, null, $pageName, $page);
	}

	/**
	 * Make a new Entity/Model with the given values
	 *
	 * @param array $values
	 *
	 * @return Model|mixed
	 */
	static function make($values)
	{
		$class = self::repository()->getClassName();

		return new $class($values);
	}

	/**
	 * Create the entity and save it to the database
	 *
	 * @param array $data
	 *
	 * @return Model|bool
	 */
	public function insert($data)
	{
		$model = self::make($data);
		if ($model->save()) {
			return $model;
		} else {
			return false;
		}
	}

	/**
	 * Update an entity and persist it to the database
	 *
	 * @param Model|int $id
	 * @param array $data
	 *
	 * @return Model|boolean
	 */
	public function update($id, $data)
	{
		$model = $this->getModel($id);
		$model->fill($data);
		if ($model->save()) {
			return $model;
		} else {
			return false;
		}
	}

	/**
	 * Save the entity to the database
	 *
	 * This will either insert or update the entity in the database
	 *
	 * @param Model $model
	 *
	 * @return bool
	 */
	public function save($model)
	{
		return $model->save();
	}

	/**
	 * Delete an record in the database
	 *
	 * @param Model|int $id
	 *
	 * @return bool|null
	 * @throws \Exception
	 */
	public function delete($id)
	{
		$model = $this->getModel($id);

		return $model->delete();
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
				'text'  => Arr::get($result, $textKey),
			];
		}

		return $list;
	}

}