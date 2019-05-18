<?php

namespace Foundry\Core\Exceptions;

/**
 * Class APIException
 *
 * @package Foundry\Exceptions
 *
 * @author Medard Ilunga
 */
class APIException {
	public static function get( $key ) {
		$reflect = null;

		try {
			$reflect = new \ReflectionClass( "App\Exceptions\\APIException" );
		} catch ( \ReflectionException $e ) {
		}

		if ( $reflect ) {
			$values = $reflect->getConstants();

			if ( array_key_exists( $key, $values ) ) {
				return $values[ $key ];
			}
		}

		return __( "An unknown error occurred." );
	}

}
