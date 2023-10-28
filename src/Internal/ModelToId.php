<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Models\ModelMap;

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
     * @return int|string
     */
    protected function getModelId(Model $model): int|string
    {
        $modelClass = get_class($model);

        return self::$models2Ids[$modelClass]
            ?? $this->fetchModelId($modelClass);
    }


    /**
     * @param string $modelClass
     *
     * @return int|string
     */
    private function fetchModelId(string $modelClass): int|string
    {
        $model = ModelMap::select('id')->where('name', $modelClass)->first();

        if ($model === null) $model = $this->createModelId($modelClass);

        self::$models2Ids[$modelClass] = $model->id;

        return $model->id;
    }


    /**
     * @param string $modelClass
     *
     * @return \Rexpl\LaravelAcl\Models\ModelMap
     */
    private function createModelId(string $modelClass): ModelMap
    {
        return ModelMap::create([
            'name' => $modelClass,
        ]);
    }
}
