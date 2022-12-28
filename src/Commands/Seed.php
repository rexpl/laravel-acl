<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands;

use Illuminate\Console\GeneratorCommand;
use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\Models\Permission;

class Seed extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:seeder {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a seed from current acl tables.';


    /**
     * Get the stub.
     * 
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/seeder.stub';
    }


    /**
     * Replace class.
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
     * Set the content of the class.
     * 
     * @param string $stub
     * 
     * @return string
     */
    protected function replaceContent($stub): string
    {
        $permissions = $this->makePermissions();
        $groups = $this->makeGroups();

        return str_replace('{{ content }}', $permissions.$groups, $stub);
    }


    /**
     * Make permissions code.
     *
     * @return string
     */
    protected function makePermissions(): string
    {
        $result = '/* Permissions */';

        foreach (Permission::all() as $permission) {
            
            $result .= sprintf(
                'Acl::newPermission(\'%s\');', $permission->name
            );
        }

        return $result;
    }


    /**
     * Make groups code.
     *
     * @return string
     */
    protected function makeGroups(): string
    {
        $result = '/* Groups */';

        foreach (Group::all() as $group) {

            // User group
            if (null !== $group->user_id) {

                $result .= sprintf(
                    'Acl::newUser(\'%s\');', $group->user_id
                );
                continue;
            }
            
            $result .= sprintf(
                'Acl::newGroup(\'%s\');', $group->name
            );
        }

        return $result;
    }
}