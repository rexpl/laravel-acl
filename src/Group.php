<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Models\ParentGroup;
use Rexpl\LaravelAcl\Models\GroupPermission;
use Rexpl\LaravelAcl\Models\Permission;

class Group
{
    /**
     * @param GroupModel $group
     * 
     * @return void
     */
    public function __construct(
        protected GroupModel $group
    ) {}


    /**
     * Return all permissions.
     * 
     * @return Collection
     */
    public function permissions(): Collection
    {
        return GroupPermission::where('group_id', $this->group->id)->get()->load('permission');
    }


    /**
     * Adds a permission to the group.
     * 
     * @param Permission|string|int $permission
     * 
     * @return void
     */
    public function addPermission(Permission|string|int $permission): void
    {
        $permission = $this->fetchPermission($permission);

        $record = new GroupPermission();

        $record->permission_id = $permission->id;
        $record->group_id = $this->group->id;

        $record->save();
    }


    /**
     * Removes a permission from the group.
     * 
     * @param Permission|string|int $permission
     * 
     * @return void
     */
    public function removePermission(Permission|string|int $permission): void
    {
        $permission = $this->fetchPermission($permission);

        GroupPermission::where('permission_id', $permission->id)
            ->where('group_id', $this->group->id)
            ->delete();
    }


    /**
     * Fetches the permission model to be sure it exist
     * 
     * @param Permission|string|int $permission
     * 
     * @return Permission
     */
    protected function fetchPermission($permission): Permission
    {
        if (is_string($permission)) {

            return Permission::firstWhere('name', $permission);
        }
        elseif (is_int($permission)) {

            return Permission::find($permission);
        }

        return $permission;
    }


    /**
     * Return all the parent groups.
     * 
     * @return Collection
     */
    public function parentGroups(): Collection
    {
        return ParentGroup::where('child_id', $this->group->id)->get()->load('parent');
    }


    /**
     * Add parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function addParentGroup(Group|int $group): void
    {
        if (is_int($group)) $group = static::find($group);

        $record = new ParentGroup();

        $record->child_id = $this->group->id;
        $record->parent_id = $group->id;

        $record->save();
    }


    /**
     * Remove parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function removeParentGroup(Group|int $group): void
    {
        if (is_int($group)) $group = static::find($group);

        ParentGroup::where('child_id', $this->group->id)
            ->where('parent_id', $group->id)
            ->delete();
    }


    /**
     * Returns all the child groups
     * 
     * @return Collection
     */
    public function childGroups(): Collection
    {
        return ParentGroup::where('child_id', $this->group->id)->get()->load('parent');
    }


    /**
     * Add parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function addChildGroups(Group|int $group): void
    {
        if (is_int($group)) $group = static::find($group);

        $record = new ParentGroup();

        $record->child_id = $group->id;
        $record->parent_id = $this->group->id;

        $record->save();
    }


    /**
     * Remove parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function removeChildGroups(Group|int $group): void
    {
        if (is_int($group)) $group = static::find($group);

        ParentGroup::where('child_id', $group->id)
            ->where('parent_id', $this->group->id)
            ->delete();
    }


    /**
     * returns the group instance.
     * 
     * @param int $id
     * 
     * @return static
     */
    public static function find(int $id): static
    {
        return new static(GroupModel::find('id', $id));
    }


    /**
     * returns the group instance.
     * 
     * @param int $id
     * 
     * @return static
     */
    public static function findUserGroup(int $id): static
    {
        return new static(GroupModel::firstWhere('user_id', $id));
    }
}