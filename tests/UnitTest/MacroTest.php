<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\UnitTest;

use BadMethodCallException;
use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Tests\TestCase;

class MacroTest extends TestCase
{
    /**
     * Test the macro functionnality.
     * 
     * @return void
     */
    public function test_macro(): void
    {
        $user = Acl::newUser(random_int(100, 999));

        Acl::macro('test', function ($userID) {
            return $this->user($userID);
        });

        $this->assertTrue(
            Acl::hasMacro('test'),
            'Failed to assert that the new macro has been correctly registered.'
        );

        $this->assertSame(
            Acl::user($user->id()),
            Acl::test($user->id()),
            'Failed to assert that the macro function is working correctly.'
        );

        Acl::clearMacros();

        $this->assertFalse(
            Acl::hasMacro('test'),
            'Failed to assert that all macros have been correctly cleared.'
        );

        $this->expectException(BadMethodCallException::class);

        Acl::test($user->id());
    }
}