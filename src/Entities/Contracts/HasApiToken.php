<?php

namespace Foundry\Core\Entities\Contracts;

interface HasApiToken {

	public function getApiToken();

	public function setApiToken(string $token);

	public function getApiTokenExpiresAt() : \DateTime;

	public function setApiTokenExpiresAt(\DateTime $token);

}