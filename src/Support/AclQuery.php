<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Rexpl\LaravelAcl\Internal\ModelToId;
use Rexpl\LaravelAcl\Internal\PackageUtility;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Rexpl\LaravelAcl\Record;

trait AclQuery
{
    use PackageUtility, ModelToId;


    /**
     * Returns the user's groups.
     *
     * @return array<int>
     */
    protected function getUserAclGroups(): array
    {
        return $this->acl()->user(Auth::id())->groups();
    }


    /**
     * Make the sub-query for all allowed id's
     *
     * @param string $action
     * @param array $range
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function makeAclSubQuery(string $action, array $range): Collection
    {
        Gate::authorize($action, static::class);

        $modelId = $this->getModelId($this);
        $groups = $this->getUserAclGroups();

        return GroupDependency::select('record_id')
            ->where('model_id', $modelId)
            ->whereIn('permission_level', $range)
            ->whereIn('group_id', $groups)
            ->get();
    }


    /**
     * Scope for insert queries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAclInsert(Builder $query): Builder
    {
        Gate::authorize('create', static::class);

        return $query;
    }


    /**
     * Scope for select queries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAclSelect(Builder $query): Builder
    {
        return $query->whereIn(
            $this->getKeyName(),
            $this->makeAclSubQuery('viewAny', Record::READ_RANGE)
        );
    }


    /**
     * Scope for update queries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAclUpdate(Builder $query): Builder
    {
        return $query->whereIn(
            $this->getKeyName(),
            $this->makeAclSubQuery('updateAny', Record::WRITE_RANGE)
        );
    }


    /**
     * Scope for delete queries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAclDelete(Builder $query): Builder
    {
        return $query->whereIn(
            $this->getKeyName(),
            $this->makeAclSubQuery('deleteAny', Record::DELETE_RANGE)
        );
    }
}
