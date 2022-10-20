<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Permission;

class ListPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:list {--g|group : See wich groups uses the permission.}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] List all permissions';


    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        if (false === $this->option('group')) {

            $this->table(
                ['ID', 'Name'],
                Permission::all()->toArray()
            );
            return;
        }
        
        $result = [];

        foreach (Permission::all() as $permission) {

            $names = [];
            
            foreach ($permission->groups()->get() as $group) {
                
                $group = $group->group()->first();

                $names[] = $group->name ?? 'user:' . $group->user_id;
            }

            $result[] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'groups' => implode(", ", $names),
            ];
        }

        $this->table(
            ['ID', 'Name', 'Groups using the permission'],
            $result
        );
    }
}