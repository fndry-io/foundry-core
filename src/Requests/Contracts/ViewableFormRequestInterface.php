<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Types\DocType;
use Foundry\Core\Inputs\Types\FormType;

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
