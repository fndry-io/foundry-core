<?php

namespace Foundry\Core\Requests\Traits;


trait HasMiddleware {

	protected $middleware = [];

	public function getMiddleware()
	{
		return $this->middleware;
	}

}