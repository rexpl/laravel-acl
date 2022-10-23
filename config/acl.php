<?php

return [

    /**
     * Iteration count. (see documentation)
     */
    'nFactor' => 3,

    /**
     * Automatically make gates for each permission.
     */
    'gates' => true,

    /**
     * Cache user's acl. (highly recommended)
     */
    'cache' => true,

    /**
     * How long to cache each users acl info.
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

];