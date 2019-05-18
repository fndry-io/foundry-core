<?php

namespace Foundry\Core\Middleware;

use Illuminate\Validation\UnauthorizedException;

/**
 * Class VerifyCsrfTokenGet
 *
 * Validates a GET call to contain a valid token
 *
 * @package Foundry\Core\Middleware
 */
class VerifyCsrfTokenGet {
	public function handle( $request, \Closure $next ) {
		$sessionToken = $request->session()->token();
		$token        = $request->input( '_token' ) ? $request->input( '_token' ) : $request->header( 'X-CSRF-TOKEN' );
		if ( ! is_string( $sessionToken ) || ! is_string( $token ) || ! hash_equals( $sessionToken, $token ) ) {
			throw new UnauthorizedException( __( 'Not authorised' ) );
		}

		return $next( $request );
	}

}