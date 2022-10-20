<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Rexpl\LaravelAcl\User;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            User::class,
            function ()
            {
                return User::find(Auth::id());
            }
        );
    }


    /**
     * Boot the package.
     * 
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->commands([

                \Rexpl\LaravelAcl\Commands\Permission\CreatePermission::class,
                \Rexpl\LaravelAcl\Commands\Permission\DeletePermission::class,
                \Rexpl\LaravelAcl\Commands\Permission\ListPermission::class,

                \Rexpl\LaravelAcl\Commands\Group\CreateGroup::class,
                \Rexpl\LaravelAcl\Commands\Group\DeleteGroup::class,
                \Rexpl\LaravelAcl\Commands\Group\ListGroup::class,

                \Rexpl\LaravelAcl\Commands\User\UserPermission::class,
                \Rexpl\LaravelAcl\Commands\User\UserGroup::class,

                \Rexpl\LaravelAcl\Commands\Record\RecordInfo::class,

            ]);

            $this->publishes([
                __DIR__.'/../../config/acl.php' => config_path('acl.php'),
            ], 'config');
        }
    
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}