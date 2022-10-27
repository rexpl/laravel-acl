<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Group;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Group;

class CreateGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:add {name? : Name for the new group}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Creates new group';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $group = Group::new(
            $this->argument('name') ?? $this->ask('Enter new group name')
        );

        $this->info('Successfuly created group (id: ' . $group->id() . ').');
    }
}