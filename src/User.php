<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Exceptions\{
    UnknownPermissionException,
    ResourceNotFoundException
};
use Rexpl\LaravelAcl\Internal\GroupPermissions;
use Rexpl\LaravelAcl\Internal\PackageUtility;
use Rexpl\LaravelAcl\Models\{
    StdAcl,
    GroupUser,
    Group as GroupModel
};

final class User
{
    use GroupPermissions, PackageUtility;

    /**
     * @param int $id
     * @param array<string> $permissions
     * @param array<int> $groups
     * @param array<\Rexpl\LaravelAcl\Internal\StdAclRow> $stdAcl
     * @param \Rexpl\LaravelAcl\Models\Group|null $group
     *
     * @return void
     */
    public function __construct(
        protected int $id = 0,
        protected array $permissions = [],
        protected array $groups = [],
        protected array $stdAcl = [],
        protected ?GroupModel $group = null
    ) {}


    /**
     * Returns the group model.
     *
     * @return \Rexpl\LaravelAcl\Models\Group
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
    protected function fetchGroupModel(): GroupModel
    {
        $this->group = GroupModel::firstWhere('user_id', $this->id);

        if (null === $this->group) {
            throw new ResourceNotFoundException(sprintf(
                'Acl group with user id: %s, not found.', $this->id
            ));
        }

        return $this->group;
    }


    /**
     * Return the user id.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }


    /**
     * Returns the user group ID.
     *
     * @return int|string
     */
    public function groupID(): int|string
    {
        return $this->group()->id;
    }


    /**
     * See if the user has the permission.
     *
     * @param string $name
     *
     * @return bool
     */
    public function canWithPermission(string $name): bool
    {
        return in_array($name, $this->permissions);
    }


    /**
     * Returns all child groups (ids).
     *
     * @return array<int>
     */
    public function groups(): array
    {
        return $this->groups;
    }


    /**
     * Returns all the permissions the user can use.
     *
     * @return array<string>
     */
    public function permissions(): array
    {
        return $this->permissions;
    }


    /**
     * Returns all the std acl of the user
     *
     * @return array<\Rexpl\LaravelAcl\Internal\StdAclRow>
     */
    public function stdAcl(): array
    {
        return $this->stdAcl;
    }


    /**
     * Add the group to the user.
     *
     * @param \Rexpl\LaravelAcl\Group|int $group
     *
     * @return static
     */
    public function addGroup(Group|int $group): static
    {
        GroupUser::create([
            'user_id' => $this->id,
            'group_id' => $this->validGroup($group)->id(),
        ]);

        return $this;
    }


    /**
     * Remove group from the user.
     *
     * @param \Rexpl\LaravelAcl\Group|int|string $group
     *
     * @return static
     */
    public function removeGroup(Group|int|string $group): static
    {
        GroupUser::where('user_id', $this->id)
            ->where('group_id', $this->validGroup($group)->id())
            ->delete();

        return $this;
    }


    /**
     * Returns all the groups where the user is in.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allGroups(): Collection
    {
        $pivotTableName = (new GroupUser())->getTable();

        return GroupModel::query()
            ->join($pivotTableName, 'id', 'group_id')
            ->where(sprintf('%s.user_id', $pivotTableName), $this->id)
            ->get();
    }


    /**
     * Add standard acl group.
     *
     * @param \Rexpl\LaravelAcl\Group|int $group
     * @param int $level
     *
     * @return static
     */
    public function addStdGroup(Group|int $group, int $level): static
    {
        if (!in_array($level, Record::FULL_RANGE)) {

            throw new UnknownPermissionException(
                'Unknown permission level ' . $level
            );
        }

        StdAcl::updateOrCreate(
            ['user_id' => $this->id, 'group_id' => $this->validGroup($group)->id()],
            ['permission_level' => $level]
        );

        return $this;
    }


    /**
     * Remove standard acl group.
     *
     * @param \Rexpl\LaravelAcl\Group|int $group
     *
     * @return static
     */
    public function removeStdGroup(Group|int $group): static
    {
        StdAcl::where('user_id', $this->id)
            ->where('group_id', $this->validGroup($group)->id())
            ->delete();

        return $this;
    }


    /**
     * Call when user creates a new record.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Rexpl\LaravelAcl\Record
     * @throws \Rexpl\LaravelAcl\Exceptions\UnknownPermissionException
     */
    public function create(Model $model): Record
    {
        $record = new Record($model);

        foreach ($this->stdAcl as $row) {

            $record->assign(
                $row->groupID,
                $row->permissionLevel
            );
        }

        return $record;
    }


    /**
     * Clear the user from the cache.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->acl()->clearUserFromCache($this->id);
    }


    /**
     * Return an updated instance of the user.
     *
     * @return static
     */
    public function fresh(): static
    {
        $this->clear();

        return $this->acl()->user($this->id);
    }


    /**
     * Refresh the instance.
     *
     * @return static
     */
    public function refresh(): static
    {
        $fresh = $this->fresh();

        $this->permissions = $fresh->permissions();
        $this->groups = $fresh->groups();
        $this->stdAcl = $fresh->stdAcl();

        return $this;
    }


    /**
     * Delete the user.
     *
     * @param bool $clean
     *
     * @return void
     */
    public function delete(bool $clean = true): void
    {
        $this->clear();

        (new Group($this->group()))->delete($clean);
    }
}
