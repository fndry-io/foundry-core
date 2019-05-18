<?php

namespace Foundry\Core\Traits;

use Foundry\Core\Models\Option;
use Illuminate\Database\Query\Builder;

/**
 * Trait Sluggify
 *
 * Create a unique slug for a given model
 *
 * @package Foundry\Traits
 *
 * @author Medard Ilunga
 *
 */
trait HasOption {

	public function options() {
		$keys = [];

		if ( method_exists( $this, 'keys' ) ) {
			$keys = call_user_func( 'keys' );
		}

		$options = Option::getApplicableOptions( function ( Builder $query ) use ( $keys ) {
			$query->leftJoin( 'option_overrides as override', 'options.id', 'override.option_id' )
			      ->whereIn( 'override.option_id', $keys );
		} );

		return $options;
	}
}
