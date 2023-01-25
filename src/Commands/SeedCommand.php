<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\Models\Permission;

class SeedCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'acl:seeder';


    /**
     * The command description
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Create a seed from current acl tables.';


    /**
     * Group parents.
     * 
     * @var string
     */
    private string $parentGroups = '/* Parent groups */';


    /**
     * Group permissions.
     * 
     * @var string
     */
    private string $groupPermissions = '/* Group permissions */';


    /**
     * Replace class.
     * 
     * We intercept the normal creation of a stub to insert the content of the stub.
     * 
     * @param string $stub
     * @param string $name
     * 
     * @return string
     */
    protected function replaceClass($stub, $name): string
    {
        $stub = $this->replaceContent($stub);

        return parent::replaceClass($stub, $name);
    }


    /**
     * Replace the content.
     * 
     * @param string $stub
     * 
     * @return string
     */
    protected function replaceContent(string $stub): string
    {
        $permissions = $this->makePermissions();
        $groups = $this->makeGroups();

        return str_replace(
            '{{ content }}',
            sprintf(
                "%s\r\n%s\r\n%s\r\n%s",
                $groups,
                $this->parentGroups,
                $permissions,
                $this->groupPermissions
            ),
            $stub
        );
    }


    /**
     * Build permission maker.
     * 
     * @return string
     */
    protected function makePermissions(): string
    {
        $result = '/* Permissions */';

        foreach (Permission::all() as $permission) {
            
            $result .= sprintf(
                '$this->permissions[\'%s\'] = Permission::create([\'name\' => \'%s\']);',
                $permission->name,
                $permission->name
            );
        }

        return $result;
    }


    /**
     * Build group makers.
     * 
     * @return string
     */
    protected function makeGroups(): string
    {
        $result = '/* Groups */';

        foreach (Group::with(['parents', 'permissions'])->get() as $group) {
            
            $result .= $this->makeSingleGroup($group);
        }

        return $result;
    }


    /**
     * Make single group.
     * 
     * @param Group $group
     * 
     * @return string
     */
    protected function makeSingleGroup(Group $group): string
    {
        $this->makeGroupParents($group);
        $this->makeGroupPermissions($group);        

        return sprintf(
            '$this->groups[\'%s\'] = Group::new(\'%s\');', $group->name, $group->name
        );
    }


    /**
     * Make code for parent groups.
     * 
     * @param Group $group
     * 
     * @return void
     */
    protected function makeGroupParents(Group $group): void
    {
        $parents = $group->parents;

        if ($parents->count() === 0) return;

        $this->parentGroups .= sprintf(
            '$this->groups[\'%s\']', $group->name
        );

        foreach ($parents as $parent) {
            
            $this->parentGroups .= sprintf(
                '->addParentGroup($this->groups[\'%s\'])', $parent->name
            );
        }

        $this->parentGroups .= ';';
    }


    /**
     * Make code for parent groups.
     * 
     * @param Group $group
     * 
     * @return void
     */
    protected function makeGroupPermissions(Group $group): void
    {
        $permissions = $group->permissions;

        if ($permissions->count() === 0) return;

        $this->groupPermissions .= sprintf(
            '$this->groups[\'%s\']', $group->name
        );

        foreach ($permissions as $permission) {
            
            $this->groupPermissions .= sprintf(
                '->addPermission($this->permissions[\'%s\'])', $permission->name
            );
        }

        $this->groupPermissions .= ';';
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/seeder.stub';
    }


    /**
     * Get the destination class path.
     *
     * @param string $name
     * 
     * @return string
     */
    protected function getPath($name): string
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

        if (is_dir($this->laravel->databasePath().'/seeds')) {
            return $this->laravel->databasePath().'/seeds/'.$name.'.php';
        }

        return $this->laravel->databasePath().'/seeders/'.$name.'.php';
    }


    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace(): string
    {
        return 'Database\Seeders\\';
    }
}