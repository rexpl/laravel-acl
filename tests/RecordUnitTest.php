<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;
use Rexpl\LaravelAcl\Record;

class RecordUnitTest extends TestBase
{
    /**
     * Test crud operations.
     * 
     * @return void
     */
    public function testCrudRecord(): void
    {
        $record = Acl::record('test', 1);

        $group = Acl::newGroup('testCrudRecord');

        $user = Acl::newUser(1);
        $user->addGroup($group);
        $user->refresh();

        $record->assign($group, Record::READ);
        $this->assertSame(
            true,
            $record->canReadRecord($user)
        );

        $record->refresh();
        
        $record->assign($group, Record::READ|Record::WRITE);
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
        $record->assign($group->id(), Record::READ|Record::WRITE|Record::DELETE);
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