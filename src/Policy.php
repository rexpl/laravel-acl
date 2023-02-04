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
     * Is booted
     * 
     * @var bool
     */
    protected static bool $isBooted = false;


    /**
     * Acronym for resource.
     * 
     * @var string
     */
    protected string $acronym = '';


    /**
     * Required permissions.
     * 
     * @var array<string,string>
     */
    protected array $permissions = [
        'read' => '',
        'write' => '',
        'delete' => '',
    ];


    /**
     * Returns the model acronym.
     * 
     * @return string
     */
    public function getModelAcronym(): string
    {
        $this->bootIfNotBooted();

        return $this->acronym;
    }


    /**
     * Boot the policy if not booted.
     * 
     * @return void
     */
    protected function bootIfNotBooted(): void
    {
        if (static::$isBooted) return;

        $this->verifyAcronym();
        $this->verifyPermissions();

        static::$isBooted = true;
    }


    /**
     * Verify the acronym is valid.
     * 
     * @return void
     * 
     * @throws \Rexpl\LaravelAcl\Exceptions\LaravelAclException
     */
    protected function verifyAcronym(): void
    {
        // We ensure we have an acronym, we know for sure it's a string (strict mode)
        if ('' !== $this->acronym) return;

        throw new LaravelAclException(sprintf(
            'No acronym defined in %s policy.', get_class($this)
        ));
    }


    /**
     * Verify the permission array is valid.
     * 
     * @return void
     * 
     * @throws \Rexpl\LaravelAcl\Exceptions\LaravelAclException
     */
    protected function verifyPermissions(): void
    {
        foreach (['read', 'write', 'delete'] as $key) {
            
            if (
                isset($this->permissions[$key])
                && ('' !== $this->permissions[$key] || null === $this->permissions[$key])
            ) continue;

            throw new LaravelAclException(sprintf(
                'Invalid or undefined permission for %s action in %s policy.', $key, get_class($this)
            ));
        }
    }


    /**
     * Returns a coorect user instance.
     * 
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * 
     * @return \Rexpl\LaravelAcl\User
     */
    protected function user(Authenticatable $user): User
    {
        // Every public method calls this method so we can boot the instance from here.
        $this->bootIfNotBooted();

        return $this->acl()->user(
            $user->getAuthIdentifier()
        );
    }


    /**
     * Verify if a user has a permission.
     * 
     * @param \Rexpl\LaravelAcl\User $user
     * @param string $action
     * 
     * @return bool
     * 
     * @throws \Rexpl\LaravelAcl\Exceptions\LaravelAclException
     */
    protected function hasPermission(User $user, string $action): bool
    {
        // We verify the action exists
        if (!isset($this->permissions[$action])) {

            throw new LaravelAclException(
                'Attempt to validate an unset permission action.'
            );
        }

        // If the required permissions has been set to null there is no permission required and we skip
        if (null === $this->permissions[$action]) return true;

        // We start a normal permission verification
        return $user->canWithPermission($this->permissions[$action]);
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
            $this->user($user), 'read'
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

        if (!$this->hasPermission($user, 'read')) return false;

        return (new Record($this->acronym, $model->getKey()))
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
            $this->user($user), 'write'
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
            $this->user($user), 'write'
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

        if (!$this->hasPermission($user, 'write')) return false;

        return (new Record($this->acronym, $model->getKey()))
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
            $this->user($user), 'delete'
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

        if (!$this->hasPermission($user, 'delete')) return false;

        return (new Record($this->acronym, $model->getKey()))
            ->canDeleteRecord($user);
    }
}