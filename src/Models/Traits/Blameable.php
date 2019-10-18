<?php

namespace Foundry\Core\Models\Traits;

use Foundry\Core\Models\Model;
use Foundry\System\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Trait Blameable
 *
 * Updates the model with who created it or updated it
 *
 * @package Foundry\Models\Contracts
 */
trait Blameable {

	public static function bootBlameable()
	{
		static::creating(function($model){
            if (!$model->created_by && ($user = Auth::user()) && self::CREATED_AT) {
				$model->setCreatedBy($user);
			}
		});
        static::updating(function($model){
            if (($user = Auth::user()) && self::UPDATED_AT) {
                $model->setUpdatedBy($user);
            }
        });
	}

    /**
     * Set the created by user
     *
     * @param User|Model $user
     */
	public function setCreatedBy($user)
    {
        $this->created_by = $user->display_name;
        $this->created_by_user_id = $user->getKey();
    }

    /**
     * Set the updated by user
     *
     * @param User|Model $user
     */
    public function setUpdatedBy($user)
    {
        $this->updated_by = $user->display_name;
        $this->updated_by_user_id = $user->getKey();
    }
}
