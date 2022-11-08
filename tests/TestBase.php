<?php

namespace Rexpl\LaravelAcl\Tests;

use Orchestra\Testbench\TestCase;

class TestBase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->getPackageProviders($this->app);
        $this->defineDatabaseMigrations();
    }


    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int, string>
     */
    protected function getPackageProviders($app)
    {
        return [
            \Rexpl\LaravelAcl\ServiceProvider\AclServiceProvider::class,
            \Staudenmeir\LaravelCte\DatabaseServiceProvider::class,
        ];
    }


    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate');
    }
}