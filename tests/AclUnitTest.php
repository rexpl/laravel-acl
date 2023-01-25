<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use App\Models\Test;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Group;
use Rexpl\LaravelAcl\Models\Permission;
use Rexpl\LaravelAcl\Record;
use Rexpl\LaravelAcl\User;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;

class AclUnitTest extends TestBase
{
    protected ModelsUser $auth1;
    protected ModelsUser $auth2;
    protected ModelsUser $auth3;
    protected ModelsUser $auth4;
    protected ModelsUser $auth5;

    /**
     * @return void
     */
    public function testTest():void
    {
        /**
         * We create a test environment.
         */

        // We start by creating users
        $this->auth1 = new ModelsUser();
        $this->auth1->id = 1;
        $user1 = User::new(1);
        $this->auth2 = new ModelsUser();
        $this->auth2->id = 2;
        $user2 = User::new(2);
        $this->auth3 = new ModelsUser();
        $this->auth3->id = 3;
        $user3 = User::new(3);
        $this->auth4 = new ModelsUser();
        $this->auth4->id = 4;
        $user4 = User::new(4);
        $this->auth5 = new ModelsUser();
        $this->auth5->id = 5;
        $user5 = User::new(5);

        // We create groups
        $group1 = Group::new('acl_test_group_1');
        $group2 = Group::new('acl_test_group_2');
        $group3 = Group::new('acl_test_group_3');
        $group4 = Group::new('acl_test_group_4');
        $group5 = Group::new('acl_test_group_5');

        // We add child/parent groups
        $group1->addParentGroup($group2);
        $group2->addParentGroup($group3->id()); // We try different options to test as much as possible
        $group4->addChildGroup($group3);
        $group5->addChildGroup($group4->id());

        // We add users to the groups
        $user1->addGroup($group1->id());
        $user2->addGroup($group2);
        $user3->addGroup($group3);
        $user4->addGroup($group4);
        $user5->addGroup($group5);

        // We create permissions
        $permissionReadID = Acl::newPermission('test:read');
        $permissionWriteID = Acl::newPermission('test:write');
        $permissionDeleteID = Acl::newPermission('test:delete');

        $group3->addPermission($permissionReadID);
        $group4->addPermission(Permission::find($permissionWriteID));
        $group5->addPermission(Acl::permissionName($permissionDeleteID));

        // We flush every cache to register every changes
        Group::flush();
        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        // We build the gates
        Acl::buildGates();

        Gate::guessPolicyNamesUsing(function () {
            return TestPolicy::class;
        });

        // test the nFactor of 3

        // We test that the gates
        Auth::login($this->auth1);

        $this->assertTrue(Gate::allows('test:read'));
        $this->assertFalse(Gate::allows('test:delete'));

        Auth::login($this->auth2);

        $this->assertTrue(Gate::allows('test:delete'));

        // We test the policies
        // We create a new record.
        $test = new Test();
        $test->id = 1;

        $user1->addStdGroup($group1, Acl::R__);
        $user1->addStdGroup($group2->id(), Acl::RW_);
        $user1->addStdGroup($group5, Acl::R_D);

        $user1->refresh();

        $user1->create('test', 1);
        $this->assertAll($test);

        $test = new Test();
        $test->id = 2;

        $user1->removeStdGroup($group5);

        $user1->create('test', 2);
        $this->assertAll($test);// should still work because of the cache

        $test = new Test();
        $test->id = 3;

        $user1->refresh();

        $user1->create('test', 3);
        $this->assertAll($test, true);

        $this->expectException(UnknownPermissionException::class);

        $user4->addStdGroup($group1, 8);
    }


    protected function assertAll($test, $last = false): void
    {
        //user 1
        Auth::login($this->auth1);
        $this->assertTrue(Gate::allows('view', $test));
        $this->assertFalse(Gate::allows('delete', $test));

        //user 2
        Auth::login($this->auth2);
        $this->assertTrue(Gate::allows('viewAny', Test::class));
        $this->assertTrue(Gate::allows('update', $test));

        //user 3
        Auth::login($this->auth3);
        $this->assertTrue(Gate::allows('create', Test::class));

        if ($last) return;

        //user 5
        Auth::login($this->auth5);
        $this->assertFalse(Gate::allows('view', $test));
        $this->assertTrue(Gate::allows('delete', $test));
    }
}