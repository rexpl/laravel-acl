<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Models\AclModel;

trait ModelToId
{
    /**
     * The already fetched model ids, to limit the number of database queries.
     *
     * @var array<string,int>
     */
    private static array $models2Ids = [];


    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return int
     */
    protected function getModelId(Model $model): int
    {
        $modelClass = get_class($model);

        return self::$models2Ids[$modelClass]
            ?? $this->fetchModelId($modelClass);
    }


    /**
     * @param string $modelClass
     *
     * @return int
     */
    private function fetchModelId(string $modelClass): int
    {
        $model = AclModel::select('id')->where('name', $modelClass)->first();

        if ($model === null) $model = $this->createModelId($modelClass);

        self::$models2Ids[$modelClass] = $model->id;

        return $model->id;
    }


    /**
     * @param string $modelClass
     *
     * @return \Rexpl\LaravelAcl\Models\AclModel
     */
    private function createModelId(string $modelClass): AclModel
    {
        return AclModel::create([
            'name' => $modelClass,
        ]);
    }
}
