<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

abstract class Service
{
    protected const DEFAULT_PER_PAGE = 10;
    protected const DEFAULT_ORDER = 'desc';
    protected const CACHE_TTL = 3600;
    
    public function __construct(
        protected readonly Model $model,
        protected readonly string $cachePrefix
    ) {
    }

    protected function remember(string $key, callable $callback): mixed
    {
        return Cache::remember($key, static::CACHE_TTL, $callback);
    }

    protected function forget(string $key): void
    {
        Cache::forget($key);
    }

    protected function getCacheKey(string $type, int $id): string
    {
        return sprintf('%s.%d.%s', $this->cachePrefix, $id, $type);
    }

    protected function clearModelCache(int $id, array $types): void
    {
        foreach ($types as $type) {
            $this->forget($this->getCacheKey($type, $id));
        }
    }

    /**
     * Limpia múltiples claves de caché relacionadas con un modelo
     *
     * @param int $id
     * @param array<string> $types
     * @param array<string> $suffixes
     * @return void
     */
    protected function clearModelCacheWithSuffixes(int $id, array $types, array $suffixes = []): void
    {
        foreach ($types as $type) {
            $this->forget($this->getCacheKey($type, $id));
            foreach ($suffixes as $suffix) {
                $this->forget($this->getCacheKey($type . '.' . $suffix, $id));
            }
        }
    }

    /**
     * @param Builder $query
     * @param string $orderBy
     * @param string $direction
     * @return Builder
     */
    protected function getOrderedQuery(
        Builder $query, 
        string $orderBy = 'created_at', 
        string $direction = self::DEFAULT_ORDER
    ): Builder {
        return $query->orderBy($orderBy, $direction);
    }

    protected function paginate(Builder $query, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}
