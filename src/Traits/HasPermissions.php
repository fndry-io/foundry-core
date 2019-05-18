<?php

namespace Foundry\Core\Traits;

trait HasPermissions {

	public function __construct() {
		if ( isset( $this->permissions ) ) {
			$method = app( 'request' )->route()->getActionMethod();
			if ( isset( $this->permissions[ $method ] ) ) {
				$this->middleware( [ 'permission:' . $this->permissions[ $method ] ] );
			} else if ( isset( $this->permissions['*'] ) ) {
				$this->middleware( [ 'permission:' . $this->permissions['*'] ] );
			}
		}
	}
}