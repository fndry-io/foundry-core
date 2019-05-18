<?php

namespace Foundry\Core\Routing;

use Illuminate\Routing\Route as LaravelRoute;

class Route extends LaravelRoute {

	protected $thenCallback;

	/**
	 * Run the route action and return the response.
	 *
	 * @return mixed
	 */
	public function run() {
		$response = parent::run();
		if ( $this->thenCallback && is_callable( $this->thenCallback ) ) {
			return call_user_func( $this->thenCallback, $response );
		} else {
			return $response;
		}
	}

	public function then( $callback ) {
		$this->thenCallback = $callback;

		return $this;
	}
}