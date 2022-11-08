<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\CommandTests;

use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\Tests\TestBase;

class GroupUnitTest extends TestBase
{
    /**
     * Test the read, write and delete command.
     *
     * @return void
     */
    public function testCommands(): void
    {
        $this->artisan('group:add')
            ->expectsQuestion('Enter new group name', 'command-unit-test')
            ->expectsOutputToContain('Successfuly created group (id: ')
            ->run();

        $group = Group::firstWhere('name', 'command-unit-test');

        $this->assertNotSame(
            null,
            $group
        );

        $this->artisan('group:delete', ['id' => $group->id])
            ->expectsOutput('Successfuly deleted group.')
            ->run();

        $this->assertNull(
            Group::find($group->id)
        );
    }   
}