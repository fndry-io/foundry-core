<?php

namespace Foundry\Core\Facades;

use Foundry\Core\Requests\Response;
use Illuminate\Support\Facades\Facade;

/**
 * Class RequestHandler
 *
 * @package Foundry\Facades
 * @see \Foundry\Core\Contracts\FormRequestHandler
 *
 * @method Response handle($key, $request, $id = null) Handle the request
 * @method Response view($key, $request, $id = null) Return a DocType of the Request for rendering it
 * @method array forms() Returns a list of the registered forms
 */
class FormRequestHandler extends Facade {
	protected static function getFacadeAccessor() {
		return 'form-request-handler';
	}
}