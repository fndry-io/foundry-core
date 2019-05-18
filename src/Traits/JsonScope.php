<?php

namespace Foundry\Core\Traits;

/**
 * Trait JsonScope
 *
 * Adds json style scopes to a model
 *
 * @package App\Models\Concerns
 */
trait JsonScope {

	/**
	 * Adds as JSON Contains where condition
	 *
	 * @param $query
	 * @param $field
	 * @param $values
	 */
	public function scopeWhereJsonContains( $query, $field, $values ) {
		$query->where( function ( $qe ) use ( $values, $field ) {
			$values = array_wrap( $values );
			$qe->whereRaw( "json_contains(`{$field}`, '[?]')", array_pop( $values ) );
			foreach ( $values as $index => $value ) {
				$qe->orWhereRaw( "json_contains(`{$field}`, '[?]')", $value );
			}
		} );
	}
}