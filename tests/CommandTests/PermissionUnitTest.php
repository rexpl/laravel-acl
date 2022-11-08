<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\CommandTests;

use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Tests\TestBase;

class PermissionUnitTest extends TestBase
{
    /**
     * Test the read, write and delete command.
     *
     * @return void
     */
    public function testCommands(): void
    {
        $this->artisan('permission:add')
            ->expectsQuestion('Enter new permission name', 'testCommands')
            ->expectsOutputToContain('Successfuly created permission (id: ')
            ->run();

        $permission = Acl::permissionID('testCommands');

        $this->assertNotSame(
            null,
            $permission
        );

        $this->artisan('permission:delete', ['id' => $permission])
            ->expectsOutput('Successfuly deleted permission.')
            ->run();

        $this->assertNull(
            Acl::permissionID('testCommands')
        );
    } 
}