<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Redis connection
     */
    public function test_redis_connection(): void
    {
        try {
            $ping = Redis::ping();
            $this->assertNotNull($ping);
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test Redis basic operations
     */
    public function test_redis_basic_operations(): void
    {
        try {
            // Set a key
            Redis::set('test_key', 'test_value');
            
            // Get the key
            $value = Redis::get('test_key');
            $this->assertEquals('test_value', $value);
            
            // Delete the key
            $deleted = Redis::del('test_key');
            $this->assertEquals(1, $deleted);
            
            // Verify key is deleted
            $value = Redis::get('test_key');
            $this->assertNull($value);
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test Redis with TTL
     */
    public function test_redis_with_ttl(): void
    {
        try {
            // Set a key with TTL
            Redis::setex('ttl_test_key', 10, 'ttl_test_value');
            
            // Get the key
            $value = Redis::get('ttl_test_key');
            $this->assertEquals('ttl_test_value', $value);
            
            // Check TTL
            $ttl = Redis::ttl('ttl_test_key');
            $this->assertGreaterThan(0, $ttl);
            $this->assertLessThanOrEqual(10, $ttl);
            
            // Clean up
            Redis::del('ttl_test_key');
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test Laravel Cache with Redis
     */
    public function test_laravel_cache_with_redis(): void
    {
        try {
            // Set cache
            Cache::put('cache_test_key', 'cache_test_value', 60);
            
            // Get cache
            $value = Cache::get('cache_test_key');
            $this->assertEquals('cache_test_value', $value);
            
            // Check if cache has key
            $this->assertTrue(Cache::has('cache_test_key'));
            
            // Forget cache
            Cache::forget('cache_test_key');
            $this->assertFalse(Cache::has('cache_test_key'));
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis cache is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test Redis test endpoint
     */
    public function test_redis_test_endpoint(): void
    {
        $response = $this->get('/redis/test');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'redis_connection',
            'test_value',
            'cached_value',
            'redis_version',
            'used_memory_human',
            'connected_clients'
        ]);
    }

    /**
     * Test setting data via API
     */
    public function test_set_data_endpoint(): void
    {
        $data = [
            'key' => 'api_test_key',
            'value' => 'api_test_value',
            'ttl' => 300
        ];

        $response = $this->post('/redis/set', $data);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'key' => 'api_test_key',
            'value' => 'api_test_value',
            'ttl' => 300
        ]);

        // Clean up
        try {
            Redis::del('api_test_key');
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    /**
     * Test getting data via API
     */
    public function test_get_data_endpoint(): void
    {
        try {
            // Set up test data
            Redis::set('get_test_key', 'get_test_value');
            
            $response = $this->get('/redis/get/get_test_key');
            
            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
                'key' => 'get_test_key',
                'value' => 'get_test_value'
            ]);
            
            // Clean up
            Redis::del('get_test_key');
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test getting non-existent data
     */
    public function test_get_nonexistent_data(): void
    {
        $response = $this->get('/redis/get/nonexistent_key');
        
        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'not_found',
            'message' => 'Key not found',
            'key' => 'nonexistent_key'
        ]);
    }

    /**
     * Test deleting data via API
     */
    public function test_delete_data_endpoint(): void
    {
        try {
            // Set up test data
            Redis::set('delete_test_key', 'delete_test_value');
            
            $response = $this->delete('/redis/delete/delete_test_key');
            
            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
                'key' => 'delete_test_key',
                'deleted' => true
            ]);
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is not available: ' . $e->getMessage());
        }
    }

    /**
     * Test dashboard page
     */
    public function test_dashboard_page(): void
    {
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Laravel 12 Dashboard');
        $response->assertSee('System Information');
        $response->assertSee('Redis Status');
    }

    /**
     * Test health check endpoint
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'version'
        ]);
        $response->assertJson(['status' => 'healthy']);
    }

    /**
     * Test cache test endpoint
     */
    public function test_cache_test_endpoint(): void
    {
        $response = $this->post('/api/redis/cache-test', ['ttl' => 30]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'stored_key',
            'stored_value',
            'retrieved_value',
            'ttl',
            'timestamp'
        ]);
    }
}
