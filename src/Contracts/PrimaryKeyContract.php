<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

interface PrimaryKeyContract
{
    /**
     * Set a unique indifferent on the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function setId(Model $model): void;


    /**
     * Migrate the column necessary for your primary key. Name must always be 'id'.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function migratePrimaryKey(Blueprint $table): ColumnDefinition;


    /**
     * Migrate the column necessary the given foreign key.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $columnName
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function migrateForeignKey(Blueprint $table, string $columnName): ColumnDefinition;


    /**
     * Whether the table is auto incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool;


    /**
     * Returns the key type (ex: int).
     *
     * @return string
     */
    public function getKeyType(): string;
}
