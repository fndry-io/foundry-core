<?php

namespace Foundry\Core\Models\Contracts;

use Carbon\Carbon;
use Foundry\Core\Models\Contracts\HasApiToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * Interface IsUser
 *
 * @property string $username
 * @property string $email
 * @property string $display_name
 * @property string $password
 * @property integer $logged_in
 * @property Carbon $last_login_at
 * @property boolean $active
 * @property array $settings
 *
 *
 * @package Foundry\System\Entities\Contracts
 */
interface IsUser extends Authenticatable, CanResetPassword, HasApiToken
{
	/**
	 * @return boolean
	 */
	public function isSuperAdmin();

    /**
     * @return boolean
     */
    public function isAdmin();

}
