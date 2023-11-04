<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\FeatureTests;

use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;
use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Internal\StdAclRow;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Rexpl\LaravelAcl\Record;
use Rexpl\LaravelAcl\Tests\test_models\TestModel;
use Rexpl\LaravelAcl\Tests\test_models\User;
use Rexpl\LaravelAcl\Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test the creation/deletion of users.
     *
     * @return void
     */
    public function test_user_crud(): void
    {
        // Create the user.
        $user = User::create([
            'name' => 'test_user_crud'
        ]);
        $userAcl = Acl::newUser($user);

        // The group id is fetched from db, so we successfully created the user because
        // a user is under the hood a group with a single user.
        $this->assertSame(
            $userAcl->groupID(),
            GroupModel::firstWhere('user_id', $user->id)->id
        );

        // Cleanup
        Acl::deleteUser($user);
        $user->delete();

        // We expect an exception because the user has been deleted.
        $this->expectException(ResourceNotFoundException::class);

        Acl::user($user);
    }


    /**
     * Test: add/remove groups to a user.
     *
     * @return void
     */
    public function test_user_groups(): void
    {
        // Create the user.
        $user = User::create([
            'name' => 'user:test_user_groups'
        ]);
        $userAcl = Acl::newUser($user);

        // We create a group.
        $group = Acl::newGroup('group:test_user_groups');

        $userAcl->addGroup($group);

        // The group has been added to the user.
        $this->assertTrue(
            $userAcl->allGroups()
                ->contains($group->id())
        );

        // The groups are cached, so the next assertion will fail without refreshing.
        $userAcl->refresh();

        $this->assertContains(
            $group->id(),
            $userAcl->groups()
        );

        $userAcl->removeGroup($group->id());

        // Correctly removed the group from the user.
        $this->assertFalse(
            $userAcl->allGroups()
                ->contains($group->id())
        );

        $user->delete();
        $userAcl->delete();
        $group->delete();
    }


    /**
     * Test: add/remove permissions to a user.
     *
     * @return void
     */
    public function test_user_permissions(): void
    {
        // Create the user.
        $user = User::create([
            'name' => 'user:test_user_permissions'
        ]);
        $userAcl = Acl::newUser($user);

        // We create a permission
        $permissionID = Acl::newPermission('permission:test_user_permissions');

        $userAcl->addPermission($permissionID);

        // The permission has been added successfully.
        $this->assertTrue(
            $userAcl->directPermissions()
                ->contains($permissionID)
        );

        // The permissions are cached, so the next assertion will fail without refreshing.
        $userAcl->refresh();

        // We also make sure it is available for the user.
        $this->assertContains(
            'permission:test_user_permissions',
            $userAcl->permissions()
        );
        $this->assertTrue(
            $userAcl->canWithPermission('permission:test_user_permissions')
        );

        $userAcl->removePermission('permission:test_user_permissions');

        // We verify the group is removed.
        $this->assertFalse(
            $userAcl->directPermissions()
                ->contains($permissionID)
        );

        $userAcl->delete();
        Acl::deletePermission($permissionID);

        $user->delete();
    }


    /**
     * Test the user std acl.
     *
     * @return void
     */
    public function test_user_std_acl(): void
    {
        // We create a user.
        $user = User::create([
            'name' => 'user:test_user_std_acl'
        ]);
        $userAcl = Acl::newUser($user);

        // We create a group.
        $group = Acl::newGroup('group:test_user_std_acl');

        $userAcl->addStdGroup($group, Record::READ);
        $userAcl->refresh();

        // The group is successfully added.
        $this->assertEquals(
            [
                new StdAclRow(Record::READ, $group->id()),
            ],
            $userAcl->stdAcl()
        );

        // We create our record.
        $record = TestModel::create([
            'name' => 'record:test_user_std_acl'
        ]);
        $recordAcl = $userAcl->create($record);

        // We now verify the group is successfully added to the new record.
        $this->assertTrue(
            $recordAcl->record()->contains(
                function (GroupDependency $row, $key) use ($group): bool {

                    if (
                        $group->id() === $row->group_id
                        && Record::READ === $row->permission_level
                    ) return true;

                    return false;
                }
            )
        );

        $userAcl->removeStdGroup($group);
        $userAcl->refresh();

        // Verify the group is successfully removed.
        $this->assertEquals(
            [],
            $userAcl->stdAcl()
        );

        $record->delete();
        $recordAcl->delete();

        $group->delete();

        $user->delete();
        $userAcl->delete();

        $this->expectException(UnknownPermissionException::class);

        // We try to add an unknown permission level to the user.
        // The user and group are already deleted, we are just after the exception.
        $userAcl->addStdGroup($group, 8);
    }


    /**
     * Test the cache functionality for useŕ retrieval.
     *
     * @return void
     */
    public function test_user_cache(): void
    {
        // We create the user.
        $user = User::create([
            'name' => 'user:test_user_cache'
        ]);
        Acl::newUser($user);

        $userAclInstance1 = Acl::user($user);
        $userAclInstance2 = Acl::user($user->id);

        $group = Acl::newGroup('group:test_user_cache');

        $userAclInstance1->addGroup($group);

        // The groups of both of these instances should be the same because there groups are
        // cached so even though we added a group in instance 1, they still ouput the same group list.
        $this->assertSame(
            $userAclInstance1->groups(),
            $userAclInstance2->groups()
        );

        $userAclInstance1->clear();
        $userAclInstance3 = Acl::user($user);

        // These should not be the same because we cleared the user from the cache.
        $this->assertNotSame(
            $userAclInstance1->groups(),
            $userAclInstance3->groups()
        );

        $userAclInstance2->refresh();

        // Instance 2 has refreshed it's data so has now the up to date group list like instance 3.
        $this->assertSame(
            $userAclInstance2->groups(),
            $userAclInstance3->groups()
        );
    }


    /**
     * We try to fetch a user with a deleted user group and make sure we get the expected exception.
     *
     * @return void
     */
    public function test_user_group_deleted(): void
    {
        // Create the user.
        $user = User::create([
            'name' => 'user:test_user_group_deleted'
        ]);
        $userAcl = Acl::newUser($user->id);

        // We save it for compare later.
        $userGroupID = $userAcl->groupID();

        // We create a group and add it otherwise we won't be able to retrieve the user once
        // the user group is deleted. Adding this dummy group allows us to retrieve the user
        // once his personnal group has been deleted.
        $group = Acl::newGroup('group:test_user_group_deleted');
        $userAcl->addGroup($group);

        // We get a fresh instance.
        $userAcl = $userAcl->fresh();

        $this->assertEquals(
            $userGroupID,
            $userAcl->groupID()
        );

        // We delete the user group manually
        GroupModel::destroy($userAcl->groupID());

        // We get a fresh instance.
        $userAcl = $userAcl->fresh();

        $this->expectException(ResourceNotFoundException::class);

        // Retrieveing the user group id will fail because the group has been deleted.
        $userAcl->groupID();
    }
}
