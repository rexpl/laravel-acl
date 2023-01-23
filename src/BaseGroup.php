<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Support\Collection;
use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Models\{
    Group as GroupModel,
    ParentGroup,
    GroupPermission,
    Permission,
    GroupDependency,
    GroupUser
};

abstract class BaseGroup
{
    /**
     * @var GroupModel|null
     */
    protected $group = null;


    /**
     * Returns the group model.
     * 
     * @return GroupModel
     */
    protected function group(): GroupModel
    {
        return $this->group ?? $this->fetchGroupModel();
    }


    /**
     * Returns the group model.
     * 
     * @return GroupModel
     */
    abstract protected function fetchGroupModel(): GroupModel;


    /**
     * Return all permissions.
     * 
     * @return Collection
     */
    public function groupPermissions(): Collection
    {
        return $this->group()->permissions()->get();
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
        $record->group_id = $this->group()->id;

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
            ->where('group_id', $this->group()->id)
            ->delete();
    }


    /**
     * Fetches the permission model to be sure it exist
     * 
     * @param Permission|string|int $permission
     * 
     * @return Permission
     */
    protected function fetchPermission(Permission|string|int $permission): Permission
    {
        if (!$permission instanceof Permission) {

            $result = is_numeric($permission)
                ? Permission::find($permission)
                : Permission::firstWhere('name', $permission);

            if (null === $result) {        
                throw new ResourceNotFoundException(
                    'Permission ' . $permission . ', not found.'
                );
            }

            $permission = $result;
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
        return $this->group()->parents()->get();
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
        if (is_int($group)) $group = Group::find($group);

        $record = new ParentGroup();

        $record->child_id = $this->group()->id;
        $record->parent_id = $group->id();

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
        if (is_int($group)) $group = Group::find($group);

        ParentGroup::where('child_id', $this->group()->id)
            ->where('parent_id', $group->id())
            ->delete();
    }


    /**
     * Returns all the child groups
     * 
     * @return Collection
     */
    public function childGroups(): Collection
    {
        return $this->group()->childrens()->get();
    }


    /**
     * Add a child group.
     * 
     * @param Group|int $group
     * 
     * @return static
     */
    public function addChildGroup(Group|int $group): static
    {
        if (is_int($group)) $group = Group::find($group);

        $record = new ParentGroup();

        $record->child_id = $group->id();
        $record->parent_id = $this->group()->id;

        $record->save();

        return $this;
    }


    /**
     * Add parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     * 
     * @deprecated 1.0 Use addChildGroup instead (without the s)
     */
    public function addChildGroups(Group|int $group): void
    {
        $this->addChildGroup($group);
    }


    /**
     * Remove the specified child group.
     * 
     * @param Group|int $group
     * 
     * @return static
     */
    public function removeChildGroup(Group|int $group): static
    {
        if (is_int($group)) $group = Group::find($group);

        ParentGroup::where('child_id', $group->id())
            ->where('parent_id', $this->group()->id)
            ->delete();

        return $this;
    }


    /**
     * Remove parent group.
     * 
     * @param Group|int $group
     * 
     * @return void
     * 
     * @deprecated 1.0 Use removeChildGroup instead (without the s)
     */
    public function removeChildGroups(Group|int $group): void
    {
        $this->removeChildGroup($group);
    }


    /**
     * Deletes the group.
     * 
     * @param bool $clean
     * 
     * @return void
     */
    public function destroy(bool $clean = true): void
    {
        static::delete($this->group()->id, $clean);
    }


    /**
     * Get the group model by ID.
     * 
     * @param int $id
     * 
     * @return GroupModel
     */
    protected static function getGroupByID(int $id): GroupModel
    {
        $group = GroupModel::find($id);

        if (null === $group) {        
            throw new ResourceNotFoundException(
                'Group with id: ' . $id . ', not found.'
            );
        }

        return $group;
    }


    /**
     * Get the group model by user ID.
     * 
     * @param int $id
     * 
     * @return GroupModel
     */
    protected static function getGroupByUserID(int $id): GroupModel
    {
        $group = GroupModel::firstWhere('user_id', $id);

        if (null === $group) {        
            throw new ResourceNotFoundException(
                'Group with user id: ' . $id . ', not found.'
            );
        }

        return $group;
    }


    /**
     * Cleans related data to group.
     * 
     * @param int $id
     * 
     * @return void
     */
    protected static function cleanGroup(int $id): void
    {
        GroupUser::where('group_id', $id)->delete();
        GroupDependency::where('group_id', $id)->delete();
        GroupPermission::where('group_id', $id)->delete();
        ParentGroup::where('child_id', $id)
            ->orWhere('parent_id', $id)->delete();
    }


    /**
     * Creates new group.
     * 
     * @param string|int $nameORuserId
     * 
     * @return static
     */
    abstract public static function new(string|int $nameORuserId): static;


    /**
     * Deletes the group by ID.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    abstract public static function delete(int $id, bool $clean = true): void;


    /**
     * Returns the group instance.
     * 
     * @param int $id
     * 
     * @return static
     */
    abstract public static function find(int $id): static;
}