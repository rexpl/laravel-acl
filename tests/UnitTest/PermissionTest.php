<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\UnitTest;

use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Models\Permission;
use Rexpl\LaravelAcl\Tests\TestCase;

class PermissionTest extends TestCase
{
    /**
     * Test the creation/deletion of permissions.
     *
     * @return void
     */
    public function test_permission_crud(): void
    {
        $id = Acl::newPermission('test_permission_crud');

        $this->assertSame(
            'test_permission_crud',
            Acl::permissionName($id)
        );

        $this->assertSame(
            $id,
            Acl::permissionID('test_permission_crud')
        );

        Acl::deletePermission($id);

        $this->assertDatabaseMissing(Permission::class, [
            'name' => 'test_permission_crud',
        ]);
    }
}
