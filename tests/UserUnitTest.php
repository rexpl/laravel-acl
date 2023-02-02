<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Models\Group as GroupModel;

class UserUnitTest extends TestBase
{
    /**
     * Test crud operations.
     * 
     * @return void
     */
    public function testUserCrud(): void
    {
        $userID = 1;
        Acl::newUser($userID);

        Acl::flush();

        $user = Acl::user($userID);
        
        /**
         * The name is fetched from db, so we successfully created the group.
         */
        $this->assertSame(
            $user->groupID(),
            GroupModel::firstWhere('user_id', $userID)->id
        );

        $user->delete();

        /**
         * We expect an exception because the group has been deleted.
         */
        $this->expectException(ResourceNotFoundException::class);

        Acl::user($userID);
    }


    /**
     * Test user cache.
     * 
     * @return void
     */
    public function testUserCache(): void
    {
        $userID = 1;
        Acl::newUser($userID);

        $userObject1 = Acl::user($userID);

        $group = Acl::newGroup('testUserCache');
        $userObject1->addGroup($group);
        
        $userObject2 = Acl::user($userID);

        $this->assertSame(
            $userObject1->groups(),
            $userObject2->groups()
        );

        $userObject1->clear();
        $userObject3 = Acl::user($userID);

        $this->assertNotSame(
            $userObject1->groups(),
            $userObject3->groups()
        );

        $userObject2->refresh();

        $this->assertSame(
            $userObject2->groups(),
            $userObject3->groups()
        );

        Acl::user($userID)->removeGroup($group);

        Acl::deleteUser($userID);
    }
}