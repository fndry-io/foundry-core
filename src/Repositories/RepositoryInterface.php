<?php

namespace Foundry\Core\Repositories;


use Doctrine\Common\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository {

	public function count(array $criteria);

	public function create($entity);

	public function update($entity);

	public function delete($entity);

}