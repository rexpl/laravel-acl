<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Models\Permission;

class GroupUnitTest extends TestBase
{
    /**
     * Test crud operation on a group.
     * 
     * @return void
     */
    public function testGroupCrud(): void
    {
        $name = 'Users';
        $group = Acl::newGroup($name);

        /**
         * The name is fetched from db, so we successfully created the group.
         */
        $this->assertSame(
            $name,
            GroupModel::find($group->id())->name
        );

        $group->destroy();

        /**
         * We expect an exception because the group has been deleted.
         */
        $this->expectException(ResourceNotFoundException::class);

        Acl::group($group->id());
    }


    /**
     * Test read, write and delete permisions on group.
     * 
     * @return void
     */
    public function testGroupPermissions(): void
    {
        $permission = 'test:testGroupPermissions';
        $permissionID = Acl::newPermission($permission);
        $group = Acl::newGroup('Users');

        $group->addPermission($permissionID);

        $this->assertSame(
            true,
            $group->groupPermissions()->contains(Permission::find($permissionID))
        );

        $group->removePermission($permissionID);

        $group->destroy();
        Acl::deletePermission($permissionID);
    }


    /**
     * Test read, write and delete parent groups on group.
     * 
     * @return void
     */
    public function testGroupParents(): void
    {
        $parent = Acl::newGroup('Parent');
        $group = Acl::newGroup('Users');

        $group->addParentGroup($parent);

        $this->assertSame(
            true,
            $group->parentGroups()->contains(GroupModel::find($parent->id()))
        );

        $group->removeParentGroup($parent);

        $group->destroy();
        $parent->destroy();
    }


    /**
     * Test read, write and delete child groups on group.
     * 
     * @return void
     */
    public function testGroupChilds(): void
    {
        $child = Acl::newGroup('Child');
        $group = Acl::newGroup('Users');

        $group->addChildGroups($child);

        $this->assertSame(
            true,
            $group->childGroups()->contains(GroupModel::find($child->id()))
        );

        $group->removeChildGroups($child);

        $group->destroy();
        $child->destroy();
    }
}