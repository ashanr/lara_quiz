<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RedisRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $maxAttempts ?: 60;
        $decayMinutes = $decayMinutes ?: 1;

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->path() .
            '|' . $request->ip()
        );
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     */
    protected function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        return $this->attempts($key) >= $maxAttempts;
    }

    /**
     * Get the number of attempts for the given key.
     */
    protected function attempts(string $key): int
    {
        return (int) Redis::get($this->getRedisKey($key)) ?: 0;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     */
    protected function hit(string $key, int $decaySeconds = 60): int
    {
        $redisKey = $this->getRedisKey($key);
        
        Redis::multi();
        Redis::incr($redisKey);
        Redis::expire($redisKey, $decaySeconds);
        $results = Redis::exec();
        
        return $results[0];
    }

    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return max(0, $maxAttempts - $this->attempts($key));
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildResponse(string $key, int $maxAttempts): JsonResponse
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        return response()->json([
            'error' => 'Too Many Attempts',
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
            'max_attempts' => $maxAttempts,
        ], 429)->header('Retry-After', $retryAfter);
    }

    /**
     * Get the number of seconds until the next retry.
     */
    protected function getTimeUntilNextRetry(string $key): int
    {
        return Redis::ttl($this->getRedisKey($key)) ?: 0;
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);

        return $response;
    }

    /**
     * Get the Redis key for rate limiting.
     */
    protected function getRedisKey(string $key): string
    {
        return 'rate_limit:' . $key;
    }
}
