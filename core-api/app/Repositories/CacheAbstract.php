<?php

namespace App\Repositories;

use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

abstract class CacheAbstract
{
    public function saveCache(string $key, callable $callback, DateTimeInterface $liveTime = null): mixed
    {
        $liveTime = $liveTime ?? now()->addDay();

        return Cache::remember($key, $liveTime, $callback);
    }

    public function clearCache(string $key): void
    {
        Cache::forget($key);
    }

    public function clearAllCache(): void
    {
        Cache::flush();
    }
}
