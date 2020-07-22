<?php

namespace Foundry\Core\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait DatabaseMigrations
{

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('migrate:fresh');

        $this->app[Kernel::class]->setArtisan(null);

        RefreshDatabaseState::$migrated = true;

        $this->beforeApplicationDestroyed(function () {
            if (!isset(self::$runOnce) || self::$runOnce !== true) {
                $this->artisan('migrate:rollback');
                RefreshDatabaseState::$migrated = false;
            }
        });
    }
}
