<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisTestController extends Controller
{
    public function index()
    {
        try {
            // Test Redis connection
            Redis::ping();
            
            // Test basic Redis operations
            Redis::set('test_key', 'Hello from Redis!');
            $value = Redis::get('test_key');
            
            // Test Cache with Redis
            Cache::put('cache_test', 'Cached value using Redis', 60);
            $cachedValue = Cache::get('cache_test');
            
            // Get Redis info
            $redisInfo = Redis::info();
            
            return response()->json([
                'status' => 'success',
                'redis_connection' => 'Connected',
                'test_value' => $value,
                'cached_value' => $cachedValue,
                'redis_version' => $redisInfo['redis_version'] ?? 'Unknown',
                'used_memory_human' => $redisInfo['used_memory_human'] ?? 'Unknown',
                'connected_clients' => $redisInfo['connected_clients'] ?? 'Unknown',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function setData(Request $request)
    {
        try {
            $key = $request->input('key', 'default_key');
            $value = $request->input('value', 'default_value');
            $ttl = $request->input('ttl', 3600); // 1 hour default
            
            // Set data in Redis with TTL
            Redis::setex($key, $ttl, $value);
            
            return response()->json([
                'status' => 'success',
                'message' => "Data set successfully",
                'key' => $key,
                'value' => $value,
                'ttl' => $ttl,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getData($key)
    {
        try {
            $value = Redis::get($key);
            $ttl = Redis::ttl($key);
            
            if ($value === null) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Key not found',
                    'key' => $key,
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'key' => $key,
                'value' => $value,
                'ttl' => $ttl,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function deleteData($key)
    {
        try {
            $deleted = Redis::del($key);
            
            return response()->json([
                'status' => 'success',
                'message' => $deleted ? 'Key deleted successfully' : 'Key not found',
                'key' => $key,
                'deleted' => (bool) $deleted,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Test caching with Laravel's Cache facade
     */
    public function testCache(Request $request)
    {
        try {
            $key = 'test_cache_' . time();
            $value = 'Cached at ' . now();
            $ttl = $request->input('ttl', 60);

            // Store in cache
            Cache::put($key, $value, $ttl);

            // Retrieve from cache
            $retrieved = Cache::get($key);

            return response()->json([
                'status' => 'success',
                'message' => 'Cache test successful',
                'stored_key' => $key,
                'stored_value' => $value,
                'retrieved_value' => $retrieved,
                'ttl' => $ttl,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }
}
