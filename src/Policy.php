<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\User as AclUser;

abstract class Policy
{
    /**
     * @return void
     */
    public function __construct()
    {

    }


    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * 
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $user = AclUser::find($user->id);

        return $user->canWithPermission($this->permissions['read']);
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Model $model
     * 
     * @return bool
     */
    public function view(User $user, Model $model): bool
    {
        $user = AclUser::find($user->id);

        if (!$user->canWithPermission($this->permissions['read'])) return false;

        $record = new Record($this->acronym, $model->id);

        return $record->canReadRecord($user);
    }


    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * 
     * @return bool
     */
    public function create(?User $user = null): bool
    {
        $user = AclUser::find($user->id);

        return $user->canWithPermission($this->permissions['write']);
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Model $model
     * 
     * @return bool
     */
    public function update(User $user, Model $model): bool
    {
        $user = AclUser::find($user->id);

        if (!$user->canWithPermission($this->permissions['write'])) return false;

        $record = new Record($this->acronym, $model->id);

        return $record->canWriteRecord($user);
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  User $user
     * @param  Model $model
     * 
     * @return bool
     */
    public function delete(User $user, Model $model): bool
    {
        $user = AclUser::find($user->id);

        if (!$user->canWithPermission($this->permissions['delete'])) return false;

        $record = new Record($this->acronym, $model->id);

        return $record->canDeleteRecord($user);
    }
}