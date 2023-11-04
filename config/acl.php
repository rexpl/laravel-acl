<?php

return [

    /**
     * Iteration count (see documentation).
     */
    'nFactor' => 3,

    /**
     * Automatically make gates for each permission.
     */
    'gates' => true,

    /**
     * Cache user's acl (highly recommended).
     */
    'cache' => true,

    /**
     * How long to cache each user acl info.
     * Duration: seconds
     */
    'duration' => 604800, // 1 week

    /**
     * Cache records acl.
     */
    'record_cache' => true,

    /**
     * How long to cache each records acl info.
     * Duration: seconds
     */
    'record_duration' => 300, // 5 minutes


    /**
     * Database configuration.
     */
    'database' => [

        /**
         * The primary key's type.
         * @implements \Rexpl\LaravelAcl\Contracts\PrimaryKeyContract
         */
        'primary_key' => \Rexpl\LaravelAcl\Support\UnsignedBigIntegerPrimaryKey::class,

        /**
         * The database connection to use.
         */
        'connection' => env('DB_CONNECTION'),

        /**
         * Table prefix, applied to all package tables.
         */
        'prefix' => 'rexpl_acl',

        /**
         * Indicates if the tables should be timestamped.
         */
        'timestamps' => false,
    ],

];
