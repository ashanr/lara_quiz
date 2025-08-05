<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // System Information
            $systemInfo = [
                'laravel_version' => app()->version(),
                'php_version' => phpversion(),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
            ];

            // Redis Information
            $redisInfo = [];
            try {
                Redis::ping();
                $info = Redis::info();
                $redisInfo = [
                    'status' => 'connected',
                    'version' => $info['redis_version'] ?? 'Unknown',
                    'uptime' => $info['uptime_in_seconds'] ?? 'Unknown',
                    'connected_clients' => $info['connected_clients'] ?? 'Unknown',
                    'used_memory_human' => $info['used_memory_human'] ?? 'Unknown',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 'Unknown',
                ];
            } catch (\Exception $e) {
                $redisInfo = [
                    'status' => 'disconnected',
                    'error' => $e->getMessage()
                ];
            }

            // Database Information
            $databaseInfo = [];
            try {
                $databaseInfo = [
                    'connection' => config('database.default'),
                    'status' => 'connected',
                    'driver' => DB::connection()->getDriverName(),
                ];
                
                if ($databaseInfo['driver'] === 'sqlite') {
                    $databaseInfo['database_file'] = database_path('database.sqlite');
                    $databaseInfo['file_exists'] = file_exists(database_path('database.sqlite'));
                    $databaseInfo['file_size'] = file_exists(database_path('database.sqlite')) 
                        ? filesize(database_path('database.sqlite')) . ' bytes' 
                        : 'N/A';
                }
            } catch (\Exception $e) {
                $databaseInfo = [
                    'status' => 'disconnected',
                    'error' => $e->getMessage()
                ];
            }

            // Cache Information
            $cacheInfo = [
                'default_store' => config('cache.default'),
                'stores' => array_keys(config('cache.stores', [])),
            ];

            return view('dashboard', compact(
                'systemInfo', 
                'redisInfo', 
                'databaseInfo', 
                'cacheInfo'
            ));

        } catch (\Exception $e) {
            return view('dashboard')->with('error', $e->getMessage());
        }
    }
}
