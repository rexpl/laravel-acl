<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Group;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Group;

class CreateGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:add {name? : Name for the new group} {--u|user= : Create a user group}';

 
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
        if (false !== $this->option('user')) {

            $this->createUserGroup();
            return;
        }

        $this->createGroup();
    }


    /**
     * Creates a regular group.
     * 
     * @return void
     */
    protected function createGroup(): void
    {
        if (!$name = $this->argument('name')) {

            $name = $this->ask('Enter new group name');
        }

        if (null !== Group::firstWhere('name', $name)) {

            $this->error('Group ' . $name . ' already exists.');
            return;
        }

        $group = new Group();
        $group->name = $name;
        $group->save();

        $this->info('Successfuly created group ' . $name . ' (id: ' . $group->id . ').');
    }


    /**
     * Creates a user group.
     * 
     * @return void
     */
    protected function createUserGroup(): void
    {
        if (!$name = $this->option('user')) {

            $name = $this->ask('Enter user id');
        }

        if (null !== Group::firstWhere('user_id', $name)) {

            $this->error('User group ' . $name . ' already exists.');
            return;
        }

        $group = new Group();
        $group->user_id = $name;
        $group->save();

        $this->info('Successfuly created group user:' . $name . ' (id: ' . $group->id . ').');
    }
}