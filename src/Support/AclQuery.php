<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Rexpl\LaravelAcl\Record;

trait AclQuery
{
    /**
     * Returns the acronym associated to the model.
     * 
     * @return Policy
     */
    protected function getModelAcronym(): string
    {
        return Gate::getPolicyFor(static::class)
            ->getModelAcronym();
    }


    /**
     * Returns the user's groups.
     * 
     * @return array<int>
     */
    protected function getUserAclGroups(): array
    {
        return Acl::user(Auth::id())->groups();
    }


    /**
     * Make the subquery for all allowed id's
     * 
     * @param string $action
     * @param array $range
     * 
     * @return Builder
     */
    protected function makeAclSubQuery(string $action, array $range): Builder
    {
        Gate::authorize($action, static::class);

        $acronym = $this->getModelAcronym();
        $groups = $this->getUserAclGroups();

        return GroupDependency::select('ressource_id')
            ->where('ressource', $acronym)
            ->whereIn('permission_level', $range)
            ->whereIn('group_id', $groups);
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