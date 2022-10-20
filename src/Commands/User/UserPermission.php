<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\User;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\User;

class UserPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:permission {id : User acl\'s info}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Display all the permissions the user can use';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $id = $this->argument('id');

        if (!is_numeric($id)) {

            $this->error('ID must be numeric.');
            return;
        }

        $user = User::find((int) $id);

        $permission = [];

        foreach ($user->permissions() as $value) {
            
            $permission[] = ['permission' => $value];
        }

        $this->table(['Permissions'], $permission);
    }
}