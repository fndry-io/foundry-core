<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Types\DocType;
use Foundry\Core\Inputs\Types\FormType;

interface ViewableFormRequestInterface {

	/**
	 * @return FormType
	 */
	public function form() : FormType;

	/**
	 * @return DocType
	 */
	public function view() : DocType;

}