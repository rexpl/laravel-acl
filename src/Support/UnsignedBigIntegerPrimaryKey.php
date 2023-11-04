<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Str;
use Rexpl\LaravelAcl\Contracts\PrimaryKeyContract;

class UnsignedBigIntegerPrimaryKey implements PrimaryKeyContract
{

    /**
     * @inheritDoc
     */
    public function setId(Model $model): void
    {
    }

    /**
     * @inheritDoc
     */
    public function migratePrimaryKey(Blueprint $table): ColumnDefinition
    {
        return $table->id();
    }

    /**
     * @inheritDoc
     */
    public function migrateForeignKey(Blueprint $table, string $columnName): ColumnDefinition
    {
        return $table->foreignId($columnName);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementing(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getKeyType(): string
    {
        return 'int';
    }
}
