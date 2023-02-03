<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use BadMethodCallException;
use Rexpl\LaravelAcl\Facades\Acl;

class MacroUnitTest extends TestBase
{
    /**
     * Test the macro functionnality.
     * 
     * @return void
     */
    public function testMacro(): void
    {
        $user = Acl::newUser(random_int(100, 999));

        Acl::macro('test', function ($userID) {
            return $this->user($userID);
        });

        $this->assertTrue(
            Acl::hasMacro('test')
        );

        $this->assertSame(
            Acl::user($user->id()),
            Acl::test($user->id())
        );

        Acl::clearMacros();

        $this->assertFalse(
            Acl::hasMacro('test')
        );

        $this->expectException(BadMethodCallException::class);

        Acl::test($user->id());
    }
}