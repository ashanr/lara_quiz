<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RedisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Redis connection event listeners
        Redis::enableEvents();

        // Log Redis connection events
        Redis::listen(function ($event) {
            if (config('app.debug')) {
                Log::debug('Redis Event', [
                    'connectionName' => $event->connectionName,
                    'command' => $event->command,
                    'parameters' => $event->parameters,
                    'time' => $event->time
                ]);
            }
        });

        // Add custom Redis commands or configurations here
        $this->registerCustomRedisCommands();
    }

    /**
     * Register custom Redis commands
     */
    private function registerCustomRedisCommands(): void
    {
        // Example of registering custom Redis commands
        // Redis::command('custom_command', CustomRedisCommand::class);
    }
}
