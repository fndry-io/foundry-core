<?php

namespace Foundry\Core\Services\Traits;

use Foundry\Core\Entities\Entity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HasRepository {

	protected $repository;

	protected function setRepository($repository)
	{
		$this->repository = $repository;
	}

	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * @param \Closure|null $builder
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return LengthAwarePaginator
	 */
	public function browse(\Closure $builder = null, $page = 1, $perPage = 20) : LengthAwarePaginator
	{
		return $this->getRepository()->filter($builder, $page, $perPage);
	}

	/**
	 * @param $id
	 *
	 * @return null|object|Entity
	 */
	public function read($id)
	{
		return $this->getRepository()->find($id);
	}

}