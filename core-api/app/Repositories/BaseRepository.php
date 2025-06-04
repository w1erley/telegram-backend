<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository extends CacheAbstract
{
    protected Model $model;

    protected string $cachePrefix;

    public function __construct(
        Model $model,
        string $cachePrefix
    )
    {
        $this->model = $model;
        $this->cachePrefix = $cachePrefix;
    }

    protected function buildCacheKey(string $method = '', mixed ...$args): string
    {
        if (empty($args)) {
            return !empty($method)
                ? "{$this->cachePrefix}_{$method}"
                : $this->cachePrefix;
        }

        $paramString = md5(json_encode($args));

        return !empty($method)
            ? "{$this->cachePrefix}_{$method}_{$paramString}"
            : "{$this->cachePrefix}_{$paramString}";
    }

    protected function clearItemCache(string $id): void {
        $oneCacheKey = $this->buildCacheKey('', $id);
        $allCacheKey = $this->buildCacheKey('all');

        $this->clearCache($oneCacheKey);
        $this->clearCache($allCacheKey);
    }

    public function all()
    {
        $cacheKey = $this->buildCacheKey('all');

        return $this->saveCache($cacheKey, function () {
            return $this->model->all();
        });
    }

    public function store(array $data)
    {
        $cacheKey = $this->buildCacheKey('all');

        $entity = $this->model->create($data);
        $this->clearCache($cacheKey);

        return $entity;
    }

    public function update(int $id, array $data)
    {
        $entity = $this->model->findOrFail($id);
        $entity->update($data);

        $this->clearItemCache($id);

        return $entity;
    }
    public function delete(int $id): bool
    {
        $entity = $this->model->findOrFail($id);
        $deleted = $entity->delete();

        $this->clearItemCache($id);

        return $deleted;
    }
    public function deleteBy(array $conditions, array $relations = []): bool
    {
        $entity = $this->findBy($conditions, $relations);
        if (!empty($entity)) {
            $deleted = $entity->delete();

            $this->clearItemCache($entity->id);

            return $deleted;
        }
        return false;
    }
    public function cacheQuery(string $suffix, callable $callback)
    {
        $cacheKey = "{$this->cachePrefix}_{$suffix}";

        return $this->saveCache($cacheKey, $callback);
    }

    public function one(int $id, array $relations = []): Model
    {
        $cacheKey = $this->buildCacheKey('', $id);

        return $this->saveCache($cacheKey, function () use ($id, $relations) {
            return $this->model->with($relations)->findOrFail($id);
        });
    }

    public function updateOrCreate(array $data, array $where): Model
    {
        $entity = $this->model->updateOrCreate($where, $data);

        $this->clearItemCache($entity->id);

        return $entity;
    }

    public function findBy(array $conditions, array $relations = []): ?Model
    {
        return $this->model->where($conditions)->with($relations)->first();
    }

    public function findCoupleBy(array $conditions, array $relations = [])
    {
        return $this->model->where($conditions)->with($relations)->get();
    }

    public function findOrCreate(array $conditions): Model
    {
        $entity = $this->model->firstOrCreate($conditions);

        return $entity;
    }
}
