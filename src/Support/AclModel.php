<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Support;

use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Contracts\PrimaryKeyContract;

trait AclModel
{
    protected string $tablePrefix;
    protected PrimaryKeyContract $primaryKeyGenerator;


    public function __construct(array $attributes = [])
    {
        // We apply the package configuration.
        $this->primaryKeyGenerator = app(PrimaryKeyContract::class);
        $this->connection = config('acl.database.connection');
        $this->timestamps = config('acl.database.timestamps');
        $this->tablePrefix = config('acl.database.prefix');
        $this->table = $this->tablePrefix . $this->tableName;

        parent::__construct($attributes);
    }


    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function bootAclModel()
    {
        // is a pivot table (where we don't need to create primary keys).
        if (static::isPivotTable()) return;

        /** @var \Rexpl\LaravelAcl\Contracts\PrimaryKeyContract $primaryKeyGenerator */
        $primaryKeyGenerator = app(PrimaryKeyContract::class);

        static::creating(function (Model $model) use ($primaryKeyGenerator) {
            $primaryKeyGenerator->setId($model);
        });
    }


    protected static function isPivotTable(): bool
    {
        return isset(static::$isPivotTable) && static::$isPivotTable;
    }


    public function getIncrementing()
    {
        return $this->primaryKeyGenerator->getIncrementing();
    }

    public function getKeyType()
    {
        return $this->primaryKeyGenerator->getKeyType();
    }
}
