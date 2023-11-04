<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rexpl\LaravelAcl\User user(\Illuminate\Contracts\Auth\Authenticatable|int|string $user) Returns the user instance of the specified id.
 * @method static \Rexpl\LaravelAcl\User newUser(\Illuminate\Contracts\Auth\Authenticatable|int|string $user) Creates a new user.
 * @method static void deleteUser(\Illuminate\Contracts\Auth\Authenticatable|int|string $user, bool $clean = true) Delete a user by id.
 * @method static \Rexpl\LaravelAcl\Group group(int|string $id) Returns the group instance of the specified id.
 * @method static \Rexpl\LaravelAcl\Group newGroup(string $name) Creates a new group.
 * @method static void deleteGroup(int|string $id, bool $clean = true) Deletes a group by id.
 * @method static \Rexpl\LaravelAcl\Record record(\Illuminate\Database\Eloquent\Model $model) Returns a record.
 * @method static void deleteRecord(\Illuminate\Database\Eloquent\Model $model) Deletes record.
 * @method static int newPermission(string $name) Create new permission. Return id.
 * @method static void deletePermission(int|string $id, bool $clean = true) Deletes permission.
 * @method static string|null permissionName(int|string $id) Returns the permission name. Returns null if permission not found.
 * @method static int|string|null permissionID(string $name) Returns the permission id. Returns null if permission not found.
 * @method static \Illuminate\Database\Eloquent\Collection permissions() Returns all the permissions.
 * @method static void flush() Flush the saved instances for long running proccesses.
 * @method static void macro(string $name, \Closure $closure) Register a custom macro.
 * @method static bool hasMacro(string $name) Check if a macro exists.
 * @method static void clearMacros() Clear all macros.
 *
 * @see \Rexpl\LaravelAcl\Acl
 */
class Acl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'rexpl-acl-facade';
    }
}
