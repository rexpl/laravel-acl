<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\ServiceProvider;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Rexpl\LaravelAcl\Acl;

class AclServiceProvider extends ServiceProvider
{
    /**
     * All commands
     *
     * @var array<string>
     */
    private array $commands = [
        \Rexpl\LaravelAcl\Commands\SeedCommand::class,

        \Rexpl\LaravelAcl\Commands\Permission\CreatePermission::class,
        \Rexpl\LaravelAcl\Commands\Permission\DeletePermission::class,
        \Rexpl\LaravelAcl\Commands\Permission\ListPermission::class,

        \Rexpl\LaravelAcl\Commands\Group\CreateGroup::class,
        \Rexpl\LaravelAcl\Commands\Group\DeleteGroup::class,
        \Rexpl\LaravelAcl\Commands\Group\ListGroup::class,
    ];
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            'rexpl-acl-facade',
            fn () => new Acl()
        );
    }


    /**
     * Boot the package.
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->mergeConfigFrom(
            __DIR__.'/../../config/acl.php', 'acl'
        );

        if ($this->app->runningInConsole()) {

            $this->commands(
                $this->commands
            );

            $this->publishes([
                __DIR__.'/../../config/acl.php' => config_path('acl.php'),
            ], 'config');
        }
        else {

           if (config('acl.gates', false)) $this->buildGates();
        }
    }


    /**
     * Build gates for each permission on package boot.
     * 
     * @return void
     */
    public function buildGates(): void
    {
        $acl = new Acl();

        foreach ($acl->permissions() as $permission) {

            Gate::define(
                $permission->name,
                fn (Authenticatable $user) => $acl->user($user->getAuthIdentifier())
                    ->canWithPermission($permission->name)
            );
        }
    }
}