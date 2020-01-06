<?php

namespace Foundry\Core\Requests\Contracts;

use Foundry\Core\Inputs\Types\FormType;
use Illuminate\Http\Request;

/**
 * Interface ViewableInputInterface
 *
 * @package Foundry\Core\Requests\Contracts
 */
interface ViewableInputInterface {

    /**
     * @param Request $request
     * @param array $params
     * @return FormType
     */
	public function form(Request $request, array $params = []) : FormType;

    /**
     * @param Request $request
     * @return FormType
     */
	public function view(Request $request) : FormType;

}
