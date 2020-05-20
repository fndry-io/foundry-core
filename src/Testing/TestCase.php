<?php

namespace Foundry\Core\Testing;

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
     * @param $value
     * @param null $guard
     * @param string $key
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function loginAs($value, $guard = null, $key = 'username')
    {
        $user = null;
        if ($value instanceof Authenticatable) {
            $user = $value;
        } elseif (is_string($value)) {
            $user = $this->getUser($value, $guard, $key);
        } else
        if ($user) {
            $this->be($user, $guard);
            $this->auth = $user;
        }
    }

    /**
     * @param $value
     * @param string $key
     * @param null $guard
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getUser($value, $guard = null, $key = 'username')
    {
        $config = $this->app->make('config');
        $provider = $config->get('auth.guards.' . $guard . '.provider');
        /** @var Authenticatable $model */
        $model = $config->get('auth.providers.' . $provider . '.model');
        return $model::query()->where($key, $value)->first();
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
