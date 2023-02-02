<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Internal\GroupPermissions;
use Rexpl\LaravelAcl\Internal\PackageUtility;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Rexpl\LaravelAcl\Models\GroupPermission;
use Rexpl\LaravelAcl\Models\GroupUser;
use Rexpl\LaravelAcl\Models\ParentGroup;

final class Group
{
    use GroupPermissions, PackageUtility;

    /**
     * @param \Rexpl\LaravelAcl\Models\Group $group
     * 
     * @return void
     */
    public function __construct(
        protected GroupModel $group
    ) {}


    /**
     * Returns the group ID.
     * 
     * @return int
     */
    public function id(): int
    {
        return $this->group->id;
    }


    /**
     * Returns the group model.
     * 
     * @return \Rexpl\LaravelAcl\Models\Group
     */
    protected function group(): GroupModel
    {
        return $this->group;
    }


    /**
     * Return all the parent groups.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function parentGroups(): Collection
    {
        return $this->group->parents()->get();
    }


    /**
     * Add parent group.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
     * 
     * @return static
     */
    public function addParentGroup(Group|int $group): static
    {
        ParentGroup::create([
            'child_id' => $this->group->id,
            'parent_id' => $this->validGroup($group)->id(),
        ]);

        return $this;
    }


    /**
     * Remove parent group.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
     * 
     * @return static
     */
    public function removeParentGroup(Group|int $group): static
    {
        ParentGroup::where('child_id', $this->group()->id)
            ->where('parent_id', $this->validGroup($group)->id())
            ->delete();

        return $this;
    }


    /**
     * Returns all the child groups
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function childGroups(): Collection
    {
        return $this->group()->childrens()->get();
    }


    /**
     * Add a child group.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
     * 
     * @return static
     */
    public function addChildGroup(Group|int $group): static
    {
        $this->validGroup($group)->addParentGroup($this);

        return $this;
    }


    /**
     * Add parent group.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
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
     * @param \Rexpl\LaravelAcl\Group|int $group
     * 
     * @return static
     */
    public function removeChildGroup(Group|int $group): static
    {
        $this->validGroup($group)->removeParentGroup($this);

        return $this;
    }


    /**
     * Remove parent group.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
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
     * Deletes the group by ID.
     * 
     * @param bool $clean
     * 
     * @return void
     */
    public function delete(bool $clean = true): void
    {
        $this->acl()->clearGroupFromSavedInstances($this->group->id);

        if ($clean) $this->cleanGroup($this->group->id);

        $this->group->delete();
    }


    /**
     * Cleans data associated to any kind of group.
     * 
     * @param int $id
     * 
     * @return void
     */
    protected function cleanGroup(int $id): void
    {
        GroupUser::where('group_id', $id)->delete();
        GroupDependency::where('group_id', $id)->delete();
        GroupPermission::where('group_id', $id)->delete();
        ParentGroup::where('child_id', $id)
            ->orWhere('parent_id', $id)->delete();
    }
}