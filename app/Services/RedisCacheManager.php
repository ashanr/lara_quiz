<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Exception;

class RedisCacheManager
{
    protected $defaultTtl = 3600; // 1 hour
    protected $keyPrefix = 'app_cache:';

    /**
     * Store data in Redis with optional TTL
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        try {
            $redisKey = $this->getFullKey($key);
            $serializedValue = serialize($value);
            $ttl = $ttl ?? $this->defaultTtl;

            if ($ttl > 0) {
                return Redis::setex($redisKey, $ttl, $serializedValue);
            } else {
                return Redis::set($redisKey, $serializedValue);
            }
        } catch (Exception $e) {
            Log::error('Redis cache put error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Retrieve data from Redis
     */
    public function get(string $key, $default = null)
    {
        try {
            $redisKey = $this->getFullKey($key);
            $value = Redis::get($redisKey);

            if ($value === null) {
                return $default;
            }

            return unserialize($value);
        } catch (Exception $e) {
            Log::error('Redis cache get error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Check if key exists in Redis
     */
    public function has(string $key): bool
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::exists($redisKey) > 0;
        } catch (Exception $e) {
            Log::error('Redis cache has error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove data from Redis
     */
    public function forget(string $key): bool
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::del($redisKey) > 0;
        } catch (Exception $e) {
            Log::error('Redis cache forget error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get or set cached data
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }

    /**
     * Increment a value in Redis
     */
    public function increment(string $key, int $value = 1): int
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::incrby($redisKey, $value);
        } catch (Exception $e) {
            Log::error('Redis cache increment error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Decrement a value in Redis
     */
    public function decrement(string $key, int $value = 1): int
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::decrby($redisKey, $value);
        } catch (Exception $e) {
            Log::error('Redis cache decrement error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get TTL for a key
     */
    public function getTtl(string $key): int
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::ttl($redisKey);
        } catch (Exception $e) {
            Log::error('Redis cache getTtl error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return -1;
        }
    }

    /**
     * Set TTL for an existing key
     */
    public function expire(string $key, int $ttl): bool
    {
        try {
            $redisKey = $this->getFullKey($key);
            return Redis::expire($redisKey, $ttl) > 0;
        } catch (Exception $e) {
            Log::error('Redis cache expire error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all keys matching a pattern
     */
    public function keys(string $pattern = '*'): array
    {
        try {
            $fullPattern = $this->getFullKey($pattern);
            $keys = Redis::keys($fullPattern);
            
            // Remove prefix from keys
            return array_map(function ($key) {
                return str_replace($this->keyPrefix, '', $key);
            }, $keys);
        } catch (Exception $e) {
            Log::error('Redis cache keys error', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Flush all cache data
     */
    public function flush(): bool
    {
        try {
            $keys = Redis::keys($this->keyPrefix . '*');
            if (empty($keys)) {
                return true;
            }
            return Redis::del($keys) > 0;
        } catch (Exception $e) {
            Log::error('Redis cache flush error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            $info = Redis::info();
            $keys = $this->keys();
            
            return [
                'total_keys' => count($keys),
                'redis_version' => $info['redis_version'] ?? 'unknown',
                'used_memory_human' => $info['used_memory_human'] ?? 'unknown',
                'connected_clients' => $info['connected_clients'] ?? 'unknown',
                'total_commands_processed' => $info['total_commands_processed'] ?? 'unknown',
                'keyspace_hits' => $info['keyspace_hits'] ?? 'unknown',
                'keyspace_misses' => $info['keyspace_misses'] ?? 'unknown',
            ];
        } catch (Exception $e) {
            Log::error('Redis cache getStats error', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get full Redis key with prefix
     */
    protected function getFullKey(string $key): string
    {
        return $this->keyPrefix . $key;
    }

    /**
     * Set the default TTL
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->defaultTtl = $ttl;
    }

    /**
     * Set the key prefix
     */
    public function setKeyPrefix(string $prefix): void
    {
        $this->keyPrefix = $prefix;
    }
}
