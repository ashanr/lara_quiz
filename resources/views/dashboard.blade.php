<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel 12 Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }
        
        .header h1 {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card h3 {
            color: #667eea;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: 500;
            color: #555;
        }
        
        .status-value {
            color: #333;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.9rem;
        }
        
        .status-connected {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-disconnected {
            color: #dc3545;
            font-weight: 600;
        }
        
        .error-card {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .footer {
            text-align: center;
            color: white;
            opacity: 0.8;
            margin-top: 2rem;
        }
        
        .api-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        
        .api-section h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .api-endpoints {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .endpoint {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 4px solid #667eea;
        }
        
        .endpoint-method {
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
        }
        
        .endpoint-url {
            font-family: 'Courier New', monospace;
            color: #333;
            margin: 0.5rem 0;
        }
        
        .endpoint-desc {
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laravel 12 Dashboard</h1>
            <p>System Status & Redis Integration</p>
        </div>
        
        @if(isset($error))
            <div class="card error-card">
                <h3>Error</h3>
                <p>{{ $error }}</p>
            </div>
        @else
            <div class="cards">
                <!-- System Information -->
                <div class="card">
                    <h3>🚀 System Information</h3>
                    @if(isset($systemInfo))
                        <div class="status-item">
                            <span class="status-label">Laravel Version</span>
                            <span class="status-value">{{ $systemInfo['laravel_version'] }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">PHP Version</span>
                            <span class="status-value">{{ $systemInfo['php_version'] }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Environment</span>
                            <span class="status-value">{{ $systemInfo['environment'] }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Debug Mode</span>
                            <span class="status-value">{{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Timezone</span>
                            <span class="status-value">{{ $systemInfo['timezone'] }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Locale</span>
                            <span class="status-value">{{ $systemInfo['locale'] }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Redis Information -->
                <div class="card">
                    <h3>🔴 Redis Status</h3>
                    @if(isset($redisInfo))
                        <div class="status-item">
                            <span class="status-label">Connection</span>
                            <span class="status-value {{ $redisInfo['status'] === 'connected' ? 'status-connected' : 'status-disconnected' }}">
                                {{ ucfirst($redisInfo['status']) }}
                            </span>
                        </div>
                        @if($redisInfo['status'] === 'connected')
                            <div class="status-item">
                                <span class="status-label">Version</span>
                                <span class="status-value">{{ $redisInfo['version'] }}</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Uptime</span>
                                <span class="status-value">{{ $redisInfo['uptime'] }}s</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Connected Clients</span>
                                <span class="status-value">{{ $redisInfo['connected_clients'] }}</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Memory Used</span>
                                <span class="status-value">{{ $redisInfo['used_memory_human'] }}</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Commands Processed</span>
                                <span class="status-value">{{ $redisInfo['total_commands_processed'] }}</span>
                            </div>
                        @else
                            <div class="status-item">
                                <span class="status-label">Error</span>
                                <span class="status-value status-disconnected">{{ $redisInfo['error'] ?? 'Unknown error' }}</span>
                            </div>
                        @endif
                    @endif
                </div>
                
                <!-- Database Information -->
                <div class="card">
                    <h3>🗄️ Database Status</h3>
                    @if(isset($databaseInfo))
                        <div class="status-item">
                            <span class="status-label">Connection</span>
                            <span class="status-value {{ $databaseInfo['status'] === 'connected' ? 'status-connected' : 'status-disconnected' }}">
                                {{ ucfirst($databaseInfo['status']) }}
                            </span>
                        </div>
                        @if($databaseInfo['status'] === 'connected')
                            <div class="status-item">
                                <span class="status-label">Default Connection</span>
                                <span class="status-value">{{ $databaseInfo['connection'] }}</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Driver</span>
                                <span class="status-value">{{ $databaseInfo['driver'] }}</span>
                            </div>
                            @if(isset($databaseInfo['database_file']))
                                <div class="status-item">
                                    <span class="status-label">Database File</span>
                                    <span class="status-value">{{ basename($databaseInfo['database_file']) }}</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-label">File Size</span>
                                    <span class="status-value">{{ $databaseInfo['file_size'] }}</span>
                                </div>
                            @endif
                        @else
                            <div class="status-item">
                                <span class="status-label">Error</span>
                                <span class="status-value status-disconnected">{{ $databaseInfo['error'] ?? 'Unknown error' }}</span>
                            </div>
                        @endif
                    @endif
                </div>
                
                <!-- Cache Information -->
                <div class="card">
                    <h3>💾 Cache Configuration</h3>
                    @if(isset($cacheInfo))
                        <div class="status-item">
                            <span class="status-label">Default Store</span>
                            <span class="status-value">{{ $cacheInfo['default_store'] }}</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Available Stores</span>
                            <span class="status-value">{{ implode(', ', $cacheInfo['stores']) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- API Endpoints Section -->
            <div class="api-section">
                <h3>🔌 Available API Endpoints</h3>
                <div class="api-endpoints">
                    <div class="endpoint">
                        <div class="endpoint-method">GET</div>
                        <div class="endpoint-url">/api/health</div>
                        <div class="endpoint-desc">Application health check</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">GET</div>
                        <div class="endpoint-url">/api/redis/status</div>
                        <div class="endpoint-desc">Redis connection status</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">POST</div>
                        <div class="endpoint-url">/api/redis/cache-test</div>
                        <div class="endpoint-desc">Test Redis caching</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">GET</div>
                        <div class="endpoint-url">/redis/test</div>
                        <div class="endpoint-desc">Redis functionality test</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">POST</div>
                        <div class="endpoint-url">/redis/set</div>
                        <div class="endpoint-desc">Set data in Redis</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">GET</div>
                        <div class="endpoint-url">/redis/get/{key}</div>
                        <div class="endpoint-desc">Get data from Redis</div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-method">DELETE</div>
                        <div class="endpoint-url">/redis/delete/{key}</div>
                        <div class="endpoint-desc">Delete data from Redis</div>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="footer">
            <p>Laravel 12 with Redis Integration • Built with ❤️</p>
            <p>Current Time: {{ now()->format('Y-m-d H:i:s T') }}</p>
        </div>
    </div>
</body>
</html>
