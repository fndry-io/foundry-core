<?php

namespace Foundry\Core\Models\Contracts;

/**
 * Interface HasAdminRights
 *
 * Helps to determine if the given Authenticatable Object on a User has admin rights
 *
 * @package Foundry\Core\Models\Contracts
 */
interface HasAdminRights
{

    /**
     * @return bool
     */
    public function isSuperAdmin(): boolean;

    /**
     * @return bool
     */
    public function isAdmin(): boolean;
}
