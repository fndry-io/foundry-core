<?php

namespace Foundry\Core\Testing;

use Foundry\System\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\Assert as PHPUnit;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('config:clear');
    }

    protected function login()
    {
        $this->loginAs('admin');
    }

    /**
     * @param string|User $username
     * @param null $guard
     */
    protected function loginAs($username, $guard = null)
    {
        $user = null;
        if (is_string($username)) {
            $user = $this->getUser($username);
        } elseif ($username instanceof User) {
            $user = $username;
        }
        if ($user) {
            $this->be($user, $guard);
            $this->auth = $user;
        }
    }

    /**
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null|Authenticatable
     */
    protected function getUser($username)
    {
        return \Foundry\System\Models\User::query()->where('username', $username)->first();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return FoundryResponse
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $headers = array_merge([
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept'           => 'application/json'
        ], $headers);
        return new FoundryResponse(parent::json($method, $uri, $data, $headers));
    }

    public function assertSubsetKeys($requiredKeys, $sourceArray)
    {
        $diff = array_diff($requiredKeys, array_keys($sourceArray));

        PHPUnit::assertEquals(
            0,
            count($diff),
            'Array does not have the required keys. Missing ' . implode(',', $diff)
        );
    }

}
