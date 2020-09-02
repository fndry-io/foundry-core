<?php

namespace Foundry\Core\EmailVerifier;

use Foundry\Core\EmailVerifier\EmailVerifier;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('email.verifier', EmailVerifier::class);
    }
}
