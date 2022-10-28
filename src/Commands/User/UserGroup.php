<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\User;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\User;

class UserGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:group {id : User acl\'s info}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Display all the groups of wich the user has access';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $user = User::find(
            (int) $this->argument('id')
        );

        $groups = [];

        foreach ($user->groups() as $value) {
            
            $group = Group::find($value);
            $groups[] = ['group' => $group->name ?? 'user:' . $group->user_id];
        }

        $this->table(['Groups'], $groups);
    }
}