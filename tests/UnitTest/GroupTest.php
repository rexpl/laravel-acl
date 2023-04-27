<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\UnitTest;

use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Tests\TestCase;

class GroupTest extends TestCase
{
    /**
     * Test the creation/deletion of groups.
     * 
     * @return void
     */
    public function test_group_crud(): void
    {
        $group = Acl::newGroup('test_group_crud');

        // The name is fetched from the db, so we successfully created the group.
        $this->assertSame(
            'test_group_crud',
            GroupModel::find($group->id())->name,
        );

        Acl::deleteGroup($group->id());

        // We expect an exception because the group has been deleted.
        $this->expectException(ResourceNotFoundException::class);

        Acl::group($group->id());
    }


    /**
     * Test the possiblity to add and delete permissions to a group.
     * 
     * @return void
     */
    public function test_group_permissions(): void
    {
        $permissionID = Acl::newPermission('permission:test_group_permissions');
        $group = Acl::newGroup('group:test_group_permissions');

        $group->addPermission($permissionID);

        // We fecth all the groups permission from db and verify our newly created permission is there.
        $this->assertTrue(
            $group->directPermissions()->contains($permissionID)
        );

        // Making sure method chaining is working :)
        $group->removePermission($permissionID)
            ->delete();

        Acl::deletePermission($permissionID);

        // We expect an exception because the permission has been deleted.
        // Note: This should not work anyway because the group has been deleted.
        $this->expectException(ResourceNotFoundException::class);

        $group->addPermission($permissionID);
    }


    /**
     * Test the possiblity to add and delete a parent to a group.
     * 
     * @return void
     */
    public function test_group_parents(): void
    {
        $group = Acl::newGroup('group');
        $parent = Acl::newGroup('parent');

        $group->addParentGroup($parent);

        $this->assertTrue(
            $group->parentGroups()->contains($parent->id())
        );

        $group->removeParentGroup($parent->id());

        $this->assertFalse(
            $group->parentGroups()->contains($parent->id())
        );

        $group->delete();
        $parent->delete();
    }


    /**
     * Test the possiblity to add and delete children to a group.
     * 
     * @return void
     */
    public function test_group_children(): void
    {
        $group = Acl::newGroup('group');
        $child = Acl::newGroup('child');

        $group->addChildGroup($child->id());

        $this->assertTrue(
            $group->childGroups()->contains($child->id())
        );

        $group->removeChildGroup($child);

        $this->assertFalse(
            $group->childGroups()->contains($child->id())
        );

        $group->delete();
        $child->delete();
    }
}