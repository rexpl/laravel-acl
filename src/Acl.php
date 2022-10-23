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
     * Creates a new group.
     * 
     * @param string $name
     * 
     * @return Group
     */
    public function newGroup(string $name): Group
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

        if (config('acl.gates', true)) {

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
    public function deletePermission(int $id): void
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
    public function permissionName(int $id): ?string
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
    public function permissionID(string $name): ?int
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