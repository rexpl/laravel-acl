<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Exceptions\LaravelAclException;
use Rexpl\LaravelAcl\Internal\PackageUtility;

abstract class Policy
{
    use PackageUtility;

    /**
     * The permission necessary to read this model.
     *
     * @var string|null
     */
    protected ?string $readPermission = null;


    /**
     * The permission necessary to write on the model.
     *
     * @var string|null
     */
    protected ?string $writePermission = null;


    /**
     * The permission necessary to delete this model.
     *
     * @var string|null
     */
    protected ?string $deletePermission = null;


    /**
     * Returns a coorect user instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return \Rexpl\LaravelAcl\User
     */
    protected function user(Authenticatable $user): User
    {
        return $this->acl()->user(
            $user->getAuthIdentifier()
        );
    }


    /**
     * Verify if a user has a permission.
     *
     * @param \Rexpl\LaravelAcl\User $user
     * @param string|null $permission
     *
     * @return bool
     */
    protected function hasPermission(User $user, ?string $permission): bool
    {
        // If the required permissions has been set to null there is no permission required, and we skip
        if (null === $permission) return true;

        // We start a normal permission verification
        return $user->canWithPermission($permission);
    }


    /**
     * Determine whether the user can view any models.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $this->hasPermission(
            $this->user($user), $this->readPermission
        );
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function view(Authenticatable $user, Model $model): bool
    {
        $user = $this->user($user);

        if (!$this->hasPermission($user, $this->readPermission)) return false;

        return (new Record($model))
            ->canReadRecord($user);
    }


    /**
     * Determine whether the user can create models.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function create(Authenticatable $user): bool
    {
        return $this->hasPermission(
            $this->user($user), $this->writePermission
        );
    }


    /**
     * Determine whether the user can update any models.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function updateAny(Authenticatable $user): bool
    {
        return $this->hasPermission(
            $this->user($user), $this->writePermission
        );
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function update(Authenticatable $user, Model $model): bool
    {
        $user = $this->user($user);

        if (!$this->hasPermission($user, $this->writePermission)) return false;

        return (new Record($model)
            ->canWriteRecord($user);
    }


    /**
     * Determine whether the user can update any models.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function deleteAny(Authenticatable $user): bool
    {
        return $this->hasPermission(
            $this->user($user), $this->deletePermission
        );
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function delete(Authenticatable $user, Model $model): bool
    {
        $user = $this->user($user);

        if (!$this->hasPermission($user, $this->deletePermission)) return false;

        return (new Record($model))
            ->canDeleteRecord($user);
    }
}
