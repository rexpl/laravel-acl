<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Internal\StdAclRow;
use Rexpl\LaravelAcl\Internal\UserData;
use Rexpl\LaravelAcl\Models\{
    Group as GroupModel,
    GroupPermission,
    GroupUser,
    Permission,
    StdAcl
};

class Acl
{
    /**
     * Cached user instances for faster access.
     *
     * @var array<int,\Rexpl\LaravelAcl\User>
     */
    protected static array $cachedUserInstances = [];


    /**
     * Cached group instances for faster access.
     *
     * @var array<int,\Rexpl\LaravelAcl\Group>
     */
    protected static array $cachedGroupInstances = [];


    /**
     * Returns the user instance of the specified id.
     * 
     * @return \Rexpl\LaravelAcl\User
     */
    public function user(int $id): User
    {
        if ($this->isUserInstanceCached($id)) return self::$cachedUserInstances[$id];

        return $this->makeNewUserInstance($id);
    }


    /**
     * Creates a new user.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\User
     */
    public function newUser(int $id): User
    {
        $group = GroupModel::create([
            'user_id' => $id,
        ]);

        GroupUser::create([
            'group_id' => $group->id,
            'user_id' => $id,
        ]);

        return self::$cachedUserInstances[$id] = new User(
            id: $id,
            group: $group
        );
    }


    /**
     * Delete a user by id.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public function deleteUser(int $id, bool $clean = true): void
    {
        $this->user($id)->delete($clean);
    }


    /**
     * Returns whether a user instance is cached or not.
     * 
     * @param int $id
     * 
     * @return bool
     */
    protected function isUserInstanceCached(int $id): bool
    {
        return isset(self::$cachedUserInstances[$id]);
    }


    /**
     * Makes a new user instance with the correct data from the given id.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\User
     */
    protected function makeNewUserInstance(int $id): User
    {
        $userData = $this->getUserData($id);

        return new User(
            $id,
            $userData->permissions,
            $userData->groups,
            $userData->stdAcl
        );
    }


    /**
     * Get the user data from the given id.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Internal\UserData
     */
    protected function getUserData(int $id): UserData
    {
        if (!config('acl.cache', true)) return $this->fetchUserData($id);

        return Cache::remember(
            'rexpl_acl_user_' . $id,
            config('acl.duration', 604800),
            fn () => $this->fetchUserData($id)
        );
    }


    /**
     * Fetch from db the needed data for the user.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Internal\UserData
     */
    protected function fetchUserData(int $id): UserData
    {
        $allDirectUserGroups = $this->fetchAllUserGroups($id);
        $nFactor = (int) config('acl.nFactor', 3);

        return new UserData(
            $this->fetchAllChildGroups($allDirectUserGroups, $nFactor),
            $this->fetchAllUserPermissions($allDirectUserGroups, $nFactor),
            $this->fetchUserStdAcl($id)
        );
    }


    /**
     * Return all the group id's for a given user.
     * 
     * @param int $id
     * 
     * @return array<int>
     */
    protected function fetchAllUserGroups(int $id): array
    {
        $allUserGroups = GroupUser::select('group_id')
            ->where('user_id', $id)
            ->get();

        if ($allUserGroups->isEmpty()) {

            throw new ResourceNotFoundException(sprintf(
                'Acl user with id: %s, not found.', $id
            ));
        }

        return $allUserGroups
            ->pluck('group_id')
            ->toArray();
    }


    /**
     * Get all child groups of an array of groups relative to n factor.
     * 
     * @param array<int> $groups
     * @param int $nFactor
     * 
     * @return array<int>
     */
    protected function fetchAllChildGroups(array $groups, int $nFactor): array
    {
        if ($nFactor === 0) return $groups;

        return $this->groupFetchQuery(
            $groups,
            $nFactor,
            'child_id'
        );
    }


    /**
     * Fetch all the permissions a user has access to.
     * 
     * @param array<int> $userGroups
     * @param int $nFactor
     * 
     * @return array<string>
     */
    protected function fetchAllUserPermissions(array $userGroups, $nFactor): array
    {
        return DB::table('acl_group_permissions as g')
            ->select('p.name')
            ->join('acl_permissions as p', 'g.permission_id', '=', 'p.id')
            ->whereIn(
                'g.group_id',
                $this->fetchAllParentGroups($userGroups, $nFactor)
            )
            ->get()
            ->pluck('name')
            ->unique()
            ->toArray();
    }


    /**
     * Get all parent groups of an array of groups relative to n factor.
     * 
     * @param array<int> $groups
     * @param int $nFactor
     * 
     * @return array<int>
     */
    protected function fetchAllParentGroups(array $groups, int $nFactor): array
    {
        if ($nFactor === 0) return $groups;

        return $this->groupFetchQuery(
            $groups,
            $nFactor,
            'parent_id'
        );
    }


    /**
     * Execute the cte query.
     * 
     * @param array<int> $groups
     * @param int $nFactor
     * @param string $retrieveColumn
     * 
     * @return array<int>
     */
    protected function groupFetchQuery(array $groups, int $nFactor, string $retrieveColumn): array
    {
        $compareColumn = $retrieveColumn === 'child_id' ? 'parent_id' : 'child_id';

        $query = DB::table('acl_parent_groups')
            ->select(DB::raw('`child_id`, `parent_id`, 1'))
            ->whereIn($compareColumn, $groups)
            ->unionAll(
                DB::table('acl_parent_groups as e')
                    ->select(DB::raw('`e`.`child_id`, `e`.`parent_id`, `ep`.`n` + 1'))
                    ->join('cte as ep',
                            'ep.' . $retrieveColumn,
                            '=',
                            'e.' . $compareColumn
                    )
                    ->where('ep.n', '<', $nFactor)
            );

        $result = DB::table('cte')
            ->select($retrieveColumn)
            ->withRecursiveExpression('cte', $query, ['child_id', 'parent_id', 'n'])
            ->get();

        return array_unique(array_merge(
            $groups,
            $result->pluck($retrieveColumn)->toArray()
        ));
    }


    /**
     * Fetches the user std acl.
     * 
     * @param int $id
     * 
     * @return array<\Rexpl\LaravelAcl\Internal\StdAclRow>
     */
    protected function fetchUserStdAcl(int $id): array
    {
        $stdAcl = StdAcl::select('group_id', 'permission_level')
            ->where('user_id', $id)
            ->get();

        return $stdAcl->map(fn (StdAcl $row) => new StdAclRow(
                $row->permission_level,
                $row->group_id
            ))->toArray();
    }


    /**
     * Returns the group instance of the specified id.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Group
     */
    public function group(int $id): Group
    {
        if ($this->isGroupInstanceCached($id)) return self::$cachedGroupInstances[$id];

        return $this->makenewGroupInstance($id);
    }


    /**
     * Creates a new group.
     * 
     * @param string $name
     * 
     * @return \Rexpl\LaravelAcl\Group
     */
    public function newGroup(string $name): Group
    {
        $group = GroupModel::create([
            'name' => $name,
        ]);

        return self::$cachedGroupInstances[$group->id] = new Group($group);
    }


    /**
     * Deletes a group by id.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public function deleteGroup(int $id, bool $clean = true): void
    {
        $this->group($id)->delete($clean);
    }


    /**
     * Returns whether a group instance is cached or not.
     * 
     * @param int $id
     * 
     * @return bool
     */
    protected function isGroupInstanceCached(int $id): bool
    {
        return isset(self::$cachedGroupInstances[$id]);
    }


    /**
     * Makes a new group instance with the correct data from the given id.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Group
     */
    protected function makeNewGroupInstance(int $id): Group
    {
        return new Group(
            $this->fetchGroupModel($id)
        );
    }


    /**
     * Fetches a group model by id.
     * 
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Models\Group
     */
    protected function fetchGroupModel(int $id): GroupModel
    {
        $group = GroupModel::find($id);

        if (null === $group) {        
            throw new ResourceNotFoundException(sprintf(
                'Acl group with id: %s, not found.', $id
            ));
        }

        return $group;
    }


    /**
     * Returns a record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return \Rexpl\LaravelAcl\Record
     */
    public function record(string $acronym, int $id): Record
    {
        return new Record($acronym, $id);
    }


    /**
     * Deletes record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return void
     */
    public function deleteRecord(string $acronym, int $id): void
    {
        $this->record($acronym, $id)->delete();
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
        return Permission::create([
            'name' => $name,
        ])->id;
    }


    /**
     * Deletes permission.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public function deletePermission(int $id, bool $clean = true): void
    {
        Permission::destroy($id);

        if (!$clean) return;

        GroupPermission::where('permission_id', $id)->delete();
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
        $permission = Permission::find($id);
        return $permission ? $permission->name : null;
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
        $permission = Permission::firstWhere('name', $name);
        return $permission ? $permission->id : null;
    }


    /**
     * Returns all the permissions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissions(): Collection
    {
        return Permission::all();
    }


    /**
     * Flush the saved instances for long running proccesses.
     * 
     * @return void
     */
    public function flush(): void
    {
        self::$cachedGroupInstances = [];
        self::$cachedUserInstances = [];
    }


    /**
     * Remove group from saved instances.
     * 
     * @param int $id
     * 
     * @return void
     */
    public function clearGroupFromSavedInstances(int $id): void
    {
        if (!$this->isGroupInstanceCached($id)) return;

        unset(self::$cachedGroupInstances[$id]);
    }


    /**
     * Remove user from saved instances.
     * 
     * @param int $id
     * 
     * @return void
     */
    public function clearUserFromCache(int $id): void
    {
        Cache::forget('rexpl_acl_user_' . $id);

        if (!$this->isUserInstanceCached($id)) return;

        unset(self::$cachedUserInstances[$id]);
    }
}