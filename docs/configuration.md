# Configuration

This package comes with a configuration file that you will find at `config/acl.php`, in this guide we are going to walk through each option of this configuration file.

<br/>

> ### :information_source: Can't find the configuration file ?
> When you install the package the configuration file first needs to be pulished before being available at `config/acl.php`. This is optional and only required if you wish to change the default values in the configuration, to publish the configuration run:
> ```
> php artisan vendor:publish --provider="Rexpl\LaravelAcl\ServiceProvider\AclServiceProvider" --tag="config"
> ```

<br/>

- [The *n* factor](#the-n-factor)
- [Gates](#gates)
- [User cache](#user-cache)
- [Record cache](#record-cache)

<br/>

## The *n* factor

``` php
'nFactor' => 3,
```

To understand what is the *n* factor parameter let's take a look at the exemple below from the [the main concept](/docs/exemples/main-exemple.md) guide.

![exemple](/docs/img/config/exemple-configuration-1.jpg)

We have here 3 groups, **Users** wich is parent of **Group 1 users** and **Group 1 users** wich is parent of **Group 4 users**. This would normally mean that **Group 4 users** inherits permissions from both **Users** and **Group 1 users**. And that **Users** inherits access from both **Group 4 users** and **Group 1 users**.

This will in some cases result in very long chains of inheritance wich will result in very expensive sql queries and hell to troubleshoot. To solve this problem there is the *n* factor, let's look again at the exemple above:

**Exemple 1: *n* factor of 2:**

![exemple](/docs/img/config/exemple-configuration-2.jpg)

**Exemple 2: *n* factor of 1:**

![exemple](/docs/img/config/exemple-configuration-3.jpg)

We see in exemple 1 that with a *n* factor of 2 **Group 4 users** can inherit the permissions from **Users**, but as demonstrated in exemple 2 with a *n* factor of 1 **Group 4 users** can't "reach" any group with permissions.

The *n* factor goes in both ways (access and permissions) meaning that in exemple 2 **Users** doesn't inherit access from **Group 4 users**.

<br/>

## Gates

``` php
'gates' => false,
```

This parameter defines whether the package should automatically define gates for each permissions. This will allow you to [authorize actions](https://laravel.com/docs/9.x/authorization#authorizing-actions-via-gates)  like any other gate in laravel. You can imagine it as an easy way to see if a user has a permission.

``` php
use Illuminate\Support\Facades\Gate;

if (Gate::allows('user:read')) {
    // The user is allowed
}
 
if (Gate::denies('user:read')) {
    // The user isn't allowed
}
```

> **Note:** the exemple above assumes that the permission **user:read** has been created beforehand. Gates will only be defined for previously created permissions.

> ### :information_source: Per record authorization
> To authorize on a per record base, please see the documentation for [policies](/docs/classes/policy.md).

<br/>

## User cache

``` php
'cache' => true,

'duration' => 604800, // 1 week
```

These options determine whether to cache (and for how long) or not the users access and permission. In this way not having to "rediscover" all parent and child groups on every request wich will quickly slow down your entire application.

> ### :warning: Updating user or related groups
> With the cache enabled adding or removing permission will not affect the user in real time. To avoid running in unexpected data breaches it is recommended to use `Rexpl\LaravelAcl\User::find($id)->clear()` to clear a specific user every time his acl is changed.

<br/>

## Record cache

``` php
'record_cache' => true,

'record_duration' => 300, // 5 minutes
```

These options determine whether to cache (and for how long) or not the users access to a specifc record. This will allow to not query the databse for each record if it is accessed often for a short period of time.

> ### :warning: Clear record cache
> There is at the moment no easy way to clear the cache just for a specific record, so any edit to a record won't be transmitted in real time to the user having accessed this record in `'record_duration'` prior to the modification. In other words:
> ```
> longer cache time = fewer database queries + longer window of exposure in case of acl modification (less safer)
> shorter cache time = more database queries + shorter window of exposure in case of acl modification (safer)
> ```
> It is the developer job to determine what is the best duration (if at all applicable), every use case is different there is no beter choice.