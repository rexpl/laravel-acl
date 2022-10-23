<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Models\Permission;

class Acl
{
    /**
     * @var array
     */
    public const READ = [4, 5, 6, 7];


    /**
     * @var array
     */
    public const WRITE = [2, 3, 6, 7];


    /**
     * @var array
     */
    public const DELETE = [1, 3, 5, 7];


    /**
     * Return a user.
     * 
     * @param int $id
     * 
     * @return User
     */
    public function user(int $id): User
    {
        return User::find($id);
    }


    /**
     * Returns a group.
     * 
     * @param int $id
     * 
     * @return Group
     */
    public function group(int $id): Group
    {
        return Group::find($id);
    }


    /**
     * Create new permission. Return id.
     * 
     * @param string $name
     * 
     * @return int
     */
    public function newPermission(string $name): int
    {
        $permission = new Permission();
        $permission->name = $name;
        $permission->save();

        return $permission->id;
    }


    /**
     * Deletes permission.
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deletePermission(int $id): void
    {
        Permission::destroy($id);
    }


    /**
     * Returns the permission name.
     * 
     * @param int $id
     * 
     * @return string
     */
    public function permissionName(int $id): string
    {
        return Permission::find($id)->name;
    }


    /**
     * Returns the permission id.
     * 
     * @param string $name
     * 
     * @return int
     */
    public function permissionID(string $name): int
    {
        return Permission::firstWhere('name', $name)->id;
    }


    /**
     * Returns all the permissions.
     * 
     * @return Collection
     */
    public function permissions(): Collection
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
        foreach (Permission::all() as $permission) {
            
            Gate::define($permission->name, function(UserModel $user) use ($permission) {
                return User::find($user->id)->canWithPermission($permission->name);
            });
        }
    }
}