<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Models\Permission;

class Acl
{   
    /**
     * Delete permission level.
     * 
     * @var int
     */
    public const __D = 1;


    /**
     * Write permission level.
     * 
     * @var int
     */
    public const _W_ = 2;


    /**
     * Write and delete permission level.
     * 
     * @var int
     */
    public const _WD = 3;


    /**
     * Read permission level.
     * 
     * @var int
     */
    public const R__ = 4;


    /**
     * Read and delete permission level.
     * 
     * @var int
     */
    public const R_D = 5;


    /**
     * Read and write permission level.
     * 
     * @var int
     */
    public const RW_ = 6;


    /**
     * Read, write and delete permission level.
     * 
     * @var int
     */
    public const RWD = 7;


    /**
     * Permission levels wich contain read permission.
     * 
     * @var array
     */
    public const READ = [4, 5, 6, 7];


    /**
     * Permission levels wich contain write permission.
     * 
     * @var array
     */
    public const WRITE = [2, 3, 6, 7];


    /**
     * Permission levels wich contain delete permission.
     * 
     * @var array
     */
    public const DELETE = [1, 3, 5, 7];


    /**
     * All allowed numbers.
     * 
     * @var array
     */
    public const RANGE = [1, 2, 3, 4, 5, 6, 7];


    /**
     * Return a user.
     * 
     * @param int $id
     * 
     * @return User
     */
    public static function user(int $id): User
    {
        return User::find($id);
    }


    /**
     * Creates a new user.
     * 
     * @param int $id
     * 
     * @return User
     */
    public static function newUser(int $id): User
    { 
        return User::new($id);
    }


    /**
     * Delete a user by id.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public static function deleteUser(int $id, bool $clean = true): void
    {
        Group::delete($id, $clean, true);
    }


    /**
     * Returns a group.
     * 
     * @param int $id
     * 
     * @return Group
     */
    public static function group(int $id): Group
    {
        return Group::find($id);
    }


    /**
     * Creates a new group.
     * 
     * @param string $name
     * 
     * @return Group
     */
    public static function newGroup(string $name): Group
    {
        return Group::new($name);
    }


    /**
     * Deletes a group by id.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public static function deleteGroup(int $id, bool $clean = true): void
    {
        Group::delete($id, $clean);
    }


    /**
     * Returns a record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return Record
     */
    public static function record(string $acronym, int $id): Record
    {
        return new Record($acronym, $id);
    }


    /**
     * Creates new record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return Record
     */
    public static function newRecord(string $acronym, int $id): Record
    {
        return Record::new($acronym, $id, []);
    }


    /**
     * Deletes record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return void
     */
    public static function deleteRecord(string $acronym, int $id): void
    {
        Record::delete($acronym, $id);
    }


    /**
     * Create new permission. Return id.
     * 
     * @param string $name
     * 
     * @return int
     */
    public static function newPermission(string $name): int
    {
        $permission = new Permission();
        $permission->name = $name;
        $permission->save();

        if (config('acl.gates', false)) {

            Gate::define($name, function(UserModel $user) use ($name) {
                return User::find($user->id)->canWithPermission($name);
            });
        }

        return $permission->id;
    }


    /**
     * Deletes permission.
     * 
     * @param int $id
     * 
     * @return void
     */
    public static function deletePermission(int $id): void
    {
        Permission::destroy($id);
    }


    /**
     * Returns the permission name. Returns null if permission not found.
     * 
     * @param int $id
     * 
     * @return string|null
     */
    public static function permissionName(int $id): ?string
    {
        return Permission::find($id)->name;
    }


    /**
     * Returns the permission id. Returns null if permission not found.
     * 
     * @param string $name
     * 
     * @return int|null
     */
    public static function permissionID(string $name): ?int
    {
        return Permission::firstWhere('name', $name)->id;
    }


    /**
     * Returns all the permissions.
     * 
     * @return Collection
     */
    public static function permissions(): Collection
    {
        return Permission::all();
    }


    /**
     * Build gates on package boot.
     * 
     * @return void
     */
    public static function buildGates(): void
    {
        foreach (static::permissions() as $permission) {
            
            Gate::define($permission->name, function(UserModel $user) use ($permission) {
                return User::find($user->id)->canWithPermission($permission->name);
            });
        }
    }
}