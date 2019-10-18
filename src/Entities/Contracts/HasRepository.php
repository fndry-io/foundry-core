<?php

namespace Foundry\Core\Entities\Contracts;

use Foundry\Core\Repositories\RepositoryInterface;

interface HasRepository
{
    public function repository() : RepositoryInterface;
}
