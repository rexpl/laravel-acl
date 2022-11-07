<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Rexpl\LaravelAcl\Models\Group as GroupModel;
use RuntimeException;

final class Group extends BaseGroup
{
    /**
     * Saves the already set instances.
     * 
     * @var array
     */
    protected static $groups = [];


    /**
     * @param GroupModel $group
     * 
     * @return void
     */
    public function __construct(GroupModel $group)
    {
        $this->group = $group;
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
        return ParentGroup::where('child_id', $this->group->id)->get()->pluck('parent');
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
        if (is_int($group)) $group = static::find($group);

        ParentGroup::where('child_id', $this->group->id)
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
        return ParentGroup::where('parent_id', $this->group->id)->get()->pluck('child');
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

        $record->child_id = $group->id();
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

        ParentGroup::where('child_id', $group->id())
            ->where('parent_id', $this->group->id)
            ->delete();
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
        if ($clean) static::cleanGroup($this->group->id);

        $this->group->delete();
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
        return new static(static::getGroupByID($id));
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
     * returns the group instance.
     * 
     * @param int $id
     * 
     * @return static
     */
    public static function findUserGroup(int $id): static
    {
        return new static(static::getGroupByUserID($id));
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
     * Deletes the group by ID.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public static function delete(int $id, bool $clean = true): void
    {
        if (isset(static::$groups[$id])) unset(static::$groups[$id]);

        $group = static::getGroupByID($id);

        if ($clean) static::cleanGroup($group->id);

        $group->delete();
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
     * @param bool $user
     * 
     * @return static
     */
    public static function new(string|int $nameORuserId, bool $user = false): static
    {
        $group = new GroupModel();

        if ($user) return static::createNewUserGroup($group, $nameORuserId);

        return static::createNewGroup($group, $nameORuserId);
    }


    /**
     * Creates new user group.
     * 
     * @param GroupModel $group
     * @param string $name       
     * 
     * @return static
     */
    protected static function createNewGroup(GroupModel $group, string $name): static
    {
        $group->name = $name;
        $group->save();

        return new static($group);
    }


    /**
     * Creates new user group.
     * 
     * @param GroupModel $group
     * @param int $id
     * 
     * @return static
     */
    protected static function createNewUserGroup(GroupModel $group, int $id): static
    {
        $group->user_id = $id;
        $group->save();

        return new static($group);
    }
}