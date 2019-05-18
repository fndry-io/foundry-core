<?php

namespace Foundry\Core\Services;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Service
 *
 * @package Foundry\Services
 *
 * @author Medard Ilunga
 */
abstract class Service {

	/**
	 * Get the model for a given id
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
	 */
	abstract static function model( $id );

}
