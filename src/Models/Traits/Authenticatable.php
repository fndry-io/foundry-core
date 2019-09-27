<?php

namespace Foundry\Core\Models\Traits;


trait Authenticatable
{
	/**
	 * Get the column name for the primary key
	 * @return string
	 */
	public function getAuthIdentifierName()
	{
		return $this->primaryKey();
	}

	/**
	 * Get the unique identifier for the user.
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		$name = $this->getAuthIdentifierName();

		return $this->{$name};
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * Get the password for the user.
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->getPassword();
	}

	/**
	 * Get the token value for the "remember me" session.
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param string $value
	 *
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}
}