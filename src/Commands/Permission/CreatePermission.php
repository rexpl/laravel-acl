<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Permission;

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
        if (!$name = $this->argument('name')) {

            $name = $this->ask('Enter new permission name');
        }

        if (null !== Permission::firstWhere('name', $name)) {

            $this->error('Permission ' . $name . ' already exists.');
            return;
        }

        $permission = new Permission();
        $permission->name = $name;
        $permission->save();

        $this->info('Successfuly created permission ' . $name . ' (id: ' . $permission->id . ').');
    }
}