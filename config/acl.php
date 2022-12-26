<?php

return [

    /**
     * Iteration count. (see documentation)
     * 
     * @var int
     */
    'nFactor' => 3,

    /**
     * Automatically make gates for each permission.
     * 
     * @var bool
     */
    'gates' => false,

    /**
     * Cache user's acl. (highly recommended)
     * 
     * @var bool
     */
    'cache' => true,

    /**
     * How long to cache each users acl info.
     * Duration: seconds
     * 
     * @var int
     */
    'duration' => 604800, // 1 week

    /**
     * Cache records acl.
     * 
     * @var bool
     */
    'record_cache' => true,

    /**
     * How long to cache each records acl info.
     * Duration: seconds
     * 
     * @var int
     */
    'record_duration' => 300, // 5 minutes

];