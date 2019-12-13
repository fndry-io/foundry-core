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

if ( ! function_exists( 'resourceUri' ) ) {
	/**
	 * Get the resource uri string unresolved (original as defined)
	 *
	 * @param string $name The name of the route
	 *
	 * @return string The resource uri
	 */
	function resourceUri($name){
		return '/' . \Illuminate\Support\Facades\Route::getRoutes()->getByName($name)->uri();
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
			if ($user = auth($guard)->user()) {
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


if ( ! function_exists( 'convert_to_moment_js' ) ) {
	function convert_to_moment_js( $format ) {
		$replacements = [
			'd' => 'DD',
			'D' => 'ddd',
			'j' => 'D',
			'l' => 'dddd',
			'N' => 'E',
			'S' => 'o',
			'w' => 'e',
			'z' => 'DDD',
			'W' => 'W',
			'F' => 'MMMM',
			'm' => 'MM',
			'M' => 'MMM',
			'n' => 'M',
			't' => '', // no equivalent
			'L' => '', // no equivalent
			'o' => 'YYYY',
			'Y' => 'YYYY',
			'y' => 'YY',
			'a' => 'a',
			'A' => 'A',
			'B' => '', // no equivalent
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'SSS',
			'e' => 'zz', // deprecated since version 1.6.0 of moment.js
			'I' => '', // no equivalent
			'O' => '', // no equivalent
			'P' => '', // no equivalent
			'T' => '', // no equivalent
			'Z' => '', // no equivalent
			'c' => '', // no equivalent
			'r' => '', // no equivalent
			'U' => 'X',
		];
		$momentFormat = strtr( $format, $replacements );

		return $momentFormat;
	}
}


if ( ! function_exists( 'get_entity_class' ) ) {
	/**
	 * Get the class name of a given entity
	 *
	 * In Doctrine, relationships are instances of proxy classes
	 *
	 * @param  $entity
	 *
	 * @return string
	 */
	function get_entity_class( $entity ) {
		if ($entity instanceof \Doctrine\ORM\Proxy\Proxy) {
			return get_parent_class($entity);
		} else {
			return get_class($entity);
		}
	}
}

if (! function_exists('object_extract')) {
	/**
	 * Extract keys from an object using "dot" notation.
	 *
	 * @param  object  $object
	 * @param  array $keys
	 * @return mixed
	 */
	function object_extract($object, $keys)
	{
		$return = [];
		foreach ($keys as $key) {
			\Illuminate\Support\Arr::set($return, $key, object_get($object, $key));
		}
		return $return;
	}
}


if (! function_exists('parse_param_string')) {

    /**
     * @param string $string The string to parse
     * @param string $delimiter The primary delimiter which separates the parameter sets
     * @param string $separator The separator that separates the key value pair
     * @return array
     */
    function parse_param_string(string $string, string $delimiter = ";", string $separator = "=")
    {
        $params = [];
        $split = explode($delimiter, $string);
        foreach ($split as $item) {
            $keyval = explode($separator, $item);
            $params[$keyval[0]] = trim($keyval[1]);
        }
        return $params;
    }
}


if (! function_exists('obj_arr_get')) {
    /**
     * Get an item from an object or array using "dot" notation.
     *
     * This does a better job than object_get as object_get can't go into cast array/json values as they are not objects
     *
     * @param  object  $object
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function obj_arr_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {

            if (is_object($object) && isset($object->{$segment})) {
                $object = $object->{$segment};
            } elseif (is_array($object) && isset($object[$segment])) {
                $object = $object[$segment];
            } else {
                return value($default);
            }


        }

        return $object;
    }
}
