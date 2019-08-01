<?php

namespace Foundry\Core\Entities\Contracts;

interface HasApiToken {

	/**
	 * Get the token
	 *
	 * @return string|null
	 */
	public function getApiToken();

	/**
	 * Set the Token
	 *
	 * @param string|null$token
	 */
	public function setApiToken($token);

	/**
	 * @return \DateTime
	 */
	public function getApiTokenExpiresAt() : \DateTime;

	/**
	 * @param \DateTime $token
	 */
	public function setApiTokenExpiresAt(\DateTime $token = null);

}