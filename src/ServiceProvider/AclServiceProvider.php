<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\User;

class AclServiceProvider extends ServiceProvider
{
    /**
     * All commands
     *
     * @var array
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
            User::class,
            fn () => User::find(Auth::id())
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

           if (config('acl.gates', false)) Acl::buildGates(); 
        }
    }
}