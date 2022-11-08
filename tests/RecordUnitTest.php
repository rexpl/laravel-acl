<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;
use Rexpl\LaravelAcl\Models\GroupUser;

class RecordUnitTest extends TestBase
{
    /**
     * Test crud operations.
     * 
     * @return void
     */
    public function testCrudRecord(): void
    {
        Acl::newRecord('test', 1);
        $record = Acl::record('test', 1);

        $group = Acl::newGroup('testCrudRecord');

        $user = Acl::newUser(1);
        $user->addGroup($group);
        $user->refresh();

        $record->assign($group, Acl::R__);
        $this->assertSame(
            true,
            $record->canReadRecord($user)
        );

        $record->refresh();
        
        $record->assign($group, Acl::RW_);
        $this->assertSame(
            true,
            $record->canWriteRecord($user)
        );
        $this->assertSame(
            false,
            $record->canDeleteRecord($user)
        );

        $record->refresh();

        /**
         * We still expect false because the result is cached.
         */
        $record->assign($group->id(), Acl::RWD);
        $this->assertSame(
            false,
            $record->canDeleteRecord($user)
        );

        $record->remove($group);

        Acl::deleteGroup($group->id());
        Acl::deleteRecord('test', 1);

        $this->expectException(UnknownPermissionException::class);
        $record->assign($group, 783);
    }
}