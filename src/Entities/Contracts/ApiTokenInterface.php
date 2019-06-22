<?php

namespace Foundry\Core\Entities\Contracts;


interface ApiTokenInterface {


	public function getApiToken();

	public function setApiToken(string $token);
}