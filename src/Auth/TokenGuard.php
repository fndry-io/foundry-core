<?php

namespace Foundry\Core\Auth;

use Carbon\Carbon;
use Foundry\Core\Entities\Contracts\HasApiToken;
use Foundry\System\Entities\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Str;
use LaravelDoctrine\ORM\Facades\EntityManager;

class TokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The name of the query string item from the request containing the API token.
     *
     * @var string
     */
    protected $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * Indicates if the API token is hashed in storage.
     *
     * @var bool
     */
    protected $hash = false;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $inputKey
     * @param  string  $storageKey
     * @param  bool  $hash
     * @return void
     */
    public function __construct(
        UserProvider $provider,
        Request $request,
        $inputKey = 'api_token',
        $storageKey = 'api_token',
        $hash = false)
    {
        $this->hash = $hash;
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->storageKey = $storageKey;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (! empty($token)) {
            $user = $this->provider->retrieveByCredentials([
                $this->storageKey => $this->hash ? hash('sha256', $token) : $token,
            ]);
        }

        return $this->user = $user;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        return $token;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [$this->storageKey => $credentials[$this->inputKey]];

	    /**
	     * @var $user HasApiToken
	     */
        if ($user = $this->provider->retrieveByCredentials($credentials)) {

	        /**
	         * We must only allow tokens where the token is still valid within the token expiry date.
	         *
	         * If they are valid, we must extend the expiry period further to ensure it remains valid
	         * for the "session" time.
	         */
        	$expires = Carbon::createFromTimestamp($user->getApiTokenExpiresAt()->getTimestamp());
        	if ($expires->greaterThanOrEqualTo(new Carbon())) {
        		$this->extendTokenExpires($user);
        		EntityManager::persist($user);
		        EntityManager::flush();
		        return true;
	        } else {
        		return false;
	        }
        }

        return false;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

	/**
	 * Sets the token on the user object
	 *
	 * @param HasApiToken $user
	 *
	 * @return string
	 */
    public function setToken(HasApiToken $user)
    {
	    $token = Str::random(60);
	    $user->setApiToken($token);
	    $this->extendTokenExpires($user);
	    return $token;
    }

    public function getToken(HasApiToken $user)
    {
    	return $user->getApiToken();
    }

	/**
	 * @param HasApiToken $user
	 */
    public function extendTokenExpires(HasApiToken $user)
    {
	    $user->setApiTokenExpiresAt(Carbon::now()->addDays(3));
    }
}
