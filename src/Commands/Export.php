<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\Models\ParentGroup;
use Rexpl\LaravelAcl\Models\Permission;

class Export extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:export {path : Path to the export file}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Export the current access control state to include it in version control';


    /**
     * Var contains the final export.
     * 
     * @var array
     */
    protected $export = [];

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        // Export permissions if needed
        if (config('acl.export.permissions', true)) $this->exportPermissions();

        // Export groups if needed.
        if (config('acl.export.groups', true)) $this->exportGroups();

        // If groups and permissions are both exported we export the morh table.
        if (
            config('acl.export.permissions', true)
            && config('acl.export.groups', true)
        ) $this->exportGroupRelations();

        // Export user relations if needed.
        if (config('acl.export.users', true)) $this->exportUserRelations();
    }


    /**
     * Export the permissions.
     * 
     * @return void
     */
    protected function exportPermissions(): void
    {
        $this->export['permissions'] = Permission::all()->toArray();
    }


    /**
     * Export the groups.
     * 
     * @return void
     */
    protected function exportGroups(): void
    {
        $this->export['groups'] = Group::all()->toArray();
    }


    /**
     * Export all the group relations.
     * 
     * @return void
     */
    protected function exportGroupRelations(): void
    {
        $this->export['group_parents'] = ParentGroup::all()->toArray();
    }
}