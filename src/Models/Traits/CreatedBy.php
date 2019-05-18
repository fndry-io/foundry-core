<?php
namespace Foundry\Core\Traits;


use Illuminate\Support\Facades\Auth;

trait CreatedBy {

	/**
	 * Laravel Model Boot function
	 */
	protected static function bootCreatedBy() {
		static::creating( function ( $model ) {
			$model[ 'created_by_user_id' ] = (Auth::user()) ? Auth::user()->id : null;
		} );
	}
}