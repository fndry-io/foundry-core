<?php

if ( ! function_exists( 'setting' ) ) {
	/**
	 * Get / set the specified setting value.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of settings key => values.
	 *
	 * @param  array|string $key
	 * @param  mixed $default
	 *
	 * @return mixed|\Foundry\Config\SettingRepository
	 */
	function setting( $key = null, $default = null ) {
		if ( is_null( $key ) ) {
			return app( 'settings' );
		}

		if ( is_array( $key ) ) {
			return app( 'settings' )->set( $key );
		}

		return app( 'settings' )->get( $key, $default );
	}

}

if ( ! function_exists( 'request_merge' ) ) {

	/**
	 * Merge additional fields into a request object
	 *
	 * @param \Illuminate\Http\Request $request | the actual request object
	 * @param array $values | associative array of key = value to be merged into the request
	 * @param null|string $key The key containing values in the request object
	 *
	 * @return \Illuminate\Http\Request
	 */
	function request_merge( \Illuminate\Http\Request $request, $values, $key = null ) {
		if ( $key ) {
			$new = $request->only( $key )[ $key ];
			foreach ( $values as $k => $v ) {
				$new[ $k ] = $v;
				$request->merge( [ $key => $new ] );
			}
		} else {
			$request->merge( $values );
		}

		return $request;
	}
}


if ( ! function_exists( 'routeUri' ) ) {
	/**
	 * Get the URI to a named route.
	 *
	 * @param  array|string $name
	 *
	 * @return string
	 */
	function routeUri( $name ) {
		if ( ! is_null( $route = app( 'router' )->getRoutes()->getByName( $name ) ) ) {
			return url( $route->uri() );
		}
		throw new InvalidArgumentException( "Route [{$name}] not defined." );
	}
}


if ( ! function_exists( 'strip_non_utf8' ) ) {
	/**
	 * Remove non utf-8 characters from a string
	 *
	 * @param $string
	 *
	 * @return string
	 */
	function strip_non_utf8( $string ) {
		return iconv( "UTF-8", "UTF-8//IGNORE", $string );
	}
}

if ( ! function_exists( 'plugin_path' ) ) {
	function plugin_path( $plugin, $path = '' ) {

		$base_path = module_path( $plugin );

		return $base_path . ( $path ? DIRECTORY_SEPARATOR . $path : $path );

	}
}

if ( ! function_exists( 'get_UTC_offset' ) ) {
	/**
	 * Gets the utc offset for a given php timezone
	 *
	 * @param $timezone
	 *
	 * @return string
	 */
	function get_UTC_offset( $timezone ) {
		$current      = timezone_open( $timezone );
		$utcTime      = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$offsetInSecs = $current->getOffset( $utcTime );
		$hoursAndSec  = gmdate( 'H:i', abs( $offsetInSecs ) );

		return stripos( $offsetInSecs, '-' ) === false ? "+{$hoursAndSec}" : "-{$hoursAndSec}";
	}
}

if ( ! function_exists( 'view_component' ) ) {
	function view_component( $name, $params ) {
		return app('view-component-handler')->handle($name, $params);
	}
}

if ( ! function_exists( 'phone_number_format' ) ) {
	function phone_number_format( $number, $pattern = "/^1?(\d{3})(\d{3})(\d{4})$/", $replacement = "($1)-$2-$3" ) {
		// Allow only Digits, remove all other characters.
		$number = preg_replace( "/[^\d]/", "", $number );

		// get number length.
		$length = strlen( $number );

		// if number = 10
		if ( $length == 10 ) {
			$number = preg_replace( $pattern, $replacement, $number );
		}

		return $number;

	}
}


if ( ! function_exists( 'auth_guard_can' ) ) {
	function auth_guard_can( $guard, $ability ) {
		$guard = (array) $guard;
		foreach ($guard as $_guard) {
			if ( \Illuminate\Support\Facades\Auth::guard($_guard)->user() && \Illuminate\Support\Facades\Auth::guard($_guard)->user()->can($ability)) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'auth_user' ) ) {
	function auth_user( $guard = null) {
		if ($guard == null) {
			$guard = array_keys(config('auth.guards'));
		}
		$guards = (array) $guard;
		foreach ($guards as $guard) {
			if ($user = auth()->guard($guard)->user()) {
				return $user;
			}
		}
		return null;
	}
}


if ( ! function_exists( 'array_from_object' ) ) {
	function array_from_object( $object, array $keys ) {
		$_object = [];
		foreach ($keys as $key) {
			\Illuminate\Support\Arr::set($_object, $key, object_get($object, $key));
		}
		return $_object;
	}
}

