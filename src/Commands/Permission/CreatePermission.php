<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Facades\Acl;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add {name? : Name for the new permission}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Creates new permission';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $id = Acl::newPermission(
            $this->argument('name') ?? $this->ask('Enter new permission name')
        );

        $this->info('Successfuly created permission (id: ' . $id . ').');
    }
}