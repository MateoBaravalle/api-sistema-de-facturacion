<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

abstract class Service
{
    protected const DEFAULT_PER_PAGE = 10;
    protected const DEFAULT_ORDER = 'desc';
    protected const CACHE_TTL = 1440;
    
    public function __construct(
        protected readonly Model $model,
        protected readonly string $cachePrefix
    ) {
    }

    protected function create(array $data): Model
    {
        return $this->model->create($data);
    }

    protected function paginate(Builder $query, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    protected function getAll(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        // return $this->remember(
        //     $this->getCacheKey('all', $page . $perPage),
        //     fn () => $this->paginate(
        //         $this->model->query(),
        //         $page,
        //         $perPage
        //     )
        // );
        return $this->paginate(
            $this->model->query(),
            $page,
            $perPage
        );
    }

    protected function getById(int $id): Model
    {
        // return $this->remember(
        //     $this->getCacheKey('id', $id),
        //     fn () => $this->model->findOrFail($id)
        // );
        return $this->model->findOrFail($id);
    }

    protected function getByIdWith(int $id, array $relation): Model
    {
        // sort($relation);
        // $cacheKey = $this->getCacheKey('with.' . implode('.', $relation), $id);

        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->model->with($relation)->findOrFail($id)
        // );
        return $this->model->with($relation)->findOrFail($id);
    }

    protected function belongsToClient(int $id): bool
    {
        $client = auth()->user()->client;
        $query = $this->model->where('id', $id);

        if (!$client) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        return $query->where('client_id', $client->id)->exists();
    }

    protected function remember(string $key, callable $callback): mixed
    {
        return Cache::remember($key, static::CACHE_TTL, $callback);
    }

    protected function forget(string $key): void
    {
        Cache::forget($key);
    }

    protected function getCacheKey(string $type, int $id = null): string
    {
        if ($id) {
            return sprintf('%s.%s.%d', $this->cachePrefix, $type, $id);
        }
        return sprintf('%s.%s', $this->cachePrefix, $type);
    }

    protected function clearModelCache(int $id, array $types): void
    {
        foreach ($types as $type) {
            $this->forget($this->getCacheKey($type, $id));
        }
        // Get the total number of pages from the latest paginator instance
        $query = $this->model->query();
        $paginator = $query->paginate(self::DEFAULT_PER_PAGE);
        $lastPage = $paginator->lastPage();

        // Clear cache for all pages
        for ($page = 1; $page <= $lastPage; $page++) {
            $this->forget($this->getCacheKey('all', $page));
        }
    }
    
    protected function clearModelCacheWithSuffixes(?int $id, array $types, array $suffixes = []): void
    {
        foreach ($types as $type) {
            $cacheKey = $id ? $this->getCacheKey($type, $id) : $this->getCacheKey($type);
            $this->forget($cacheKey);

            foreach ($suffixes as $suffix) {
                $cacheKeyWithSuffix = $id
                    ? $this->getCacheKey($type . '.' . $suffix, $id)
                    : $this->getCacheKey($type . '.' . $suffix);
                $this->forget($cacheKeyWithSuffix);
            }
        }
    }
}
