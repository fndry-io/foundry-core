<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Types\DocType;
use Foundry\Core\Inputs\Types\FormType;

/**
 * Interface ViewableFormRequestInterface
 *
 * This interface works with the older FormRequests which where designed to contain the view and handle methods
 *
 *
 *
 * @package Foundry\Core\Requests\Contracts
 * @deprecated use Requests\Contracts\ViewableInputInterface with the new Foundry\System\Http\Controllers\Controller::viewOrHandle
 */
interface ViewableFormRequestInterface {

	/**
	 * @return FormType
	 */
	function form($params = []) : FormType;

	/**
	 * @return FormType
	 */
	public function view() : FormType;

}
