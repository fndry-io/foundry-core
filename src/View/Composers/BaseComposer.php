<?php

namespace Foundry\Core\View\Composers;

use Illuminate\Http\Request;

abstract class BaseComposer {

	protected $request;

	/**
	 * @param Request $request
	 * @return void
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

}