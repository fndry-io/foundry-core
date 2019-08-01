<?php

namespace Foundry\Core\Entities\Traits;

trait ApiTokenable {

	/**
	 * @var string The Token
	 */
	protected $api_token;

	/**
	 * @var \DateTime
	 */
	protected $api_token_expires_at;

	public function getApiToken(){
		return $this->api_token;
	}

	/**
	 * @param string|null $token
	 */
	public function setApiToken($token){
		$this->api_token = $token;
	}

	public function setApiTokenExpiresAt(\DateTime $expires_at = null){
		$this->api_token_expires_at = $expires_at;
	}

	public function getApiTokenExpiresAt() : \DateTime {
		return $this->api_token_expires_at;
	}

}