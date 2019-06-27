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
 * @method static Response handle($key, $request, $id = null) Handle the request
 * @method static Response view($key, $request, $id = null) Return a DocType of the Request for rendering it
 * @method static array forms() Returns a list of the registered forms
 * @method static void register($class) Register a form request class
 * @method static void route($uri, $class) Registers the request form class and adds the route to it
 */
class FormRequestHandler extends Facade {
	protected static function getFacadeAccessor() {
		return 'form-request-handler';
	}
}