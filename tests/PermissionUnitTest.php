<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Facades\Acl;

class PermissionUnitTest extends TestBase
{
    /**
     * Test crud operations.
     * 
     * @return void
     */
    public function testCrudPermission(): void
    {
        $name = 'testCrudPermission';
        $id = Acl::newPermission($name);

        $testName = Acl::permissionName($id);
        $this->assertSame(
            $name,
            $testName
        );

        $testID = Acl::permissionID($testName);
        $this->assertSame(
            $id,
            $testID
        );

        $this->assertSame(
            true,
            Acl::permissions()->contains($id)
        );

        Acl::deletePermission($id);

        $this->assertSame(
            null,
            Acl::permissionName($id)
        );
    }
}