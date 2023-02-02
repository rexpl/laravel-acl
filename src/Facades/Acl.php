<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rexpl\LaravelAcl\User user(int $id) Returns the user instance of the specified id.
 * @method static \Rexpl\LaravelAcl\User newUser(int $id) Creates a new user.
 * @method static void deleteUser(int $id, bool $clean) Delete a user by id.
 * @method static \Rexpl\LaravelAcl\Group group(int $id) Returns the group instance of the specified id.
 * @method static \Rexpl\LaravelAcl\Group newGroup(string $name) Creates a new group.
 * @method static void deleteGroup(int $id, bool $clean = true) Deletes a group by id.
 * @method static \Rexpl\LaravelAcl\Record record(string $acronym, int $id) Returns a record.
 * @method static void deleteRecord(string $acronym, int $id) Deletes record.
 * @method static int newPermission(string $name) Create new permission. Return id.
 * @method static void deletePermission(int $id, bool $clean = true) Deletes permission.
 * @method static string|null permissionName(int $id) Returns the permission name. Returns null if permission not found.
 * @method static int|null permissionID(string $name) Returns the permission id. Returns null if permission not found.
 * @method static \Illuminate\Database\Eloquent\Collection permissions() Returns all the permissions.
 * @method static void flush() Flush the saved instances for long running proccesses.
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