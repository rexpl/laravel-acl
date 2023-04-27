<?php

namespace Rexpl\LaravelAcl\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Gate::policy(
            Rexpl\LaravelAcl\Tests\Models\TestModel::class,
            Rexpl\LaravelAcl\Tests\Policies\TestPolicy::class
        );
    }


    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int,string>
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
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
