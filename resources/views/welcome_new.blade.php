<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel 12 with Redis</title>
    
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .container {
            text-align: center;
            max-width: 800px;
            padding: 2rem;
        }
        
        .logo {
            font-size: 4rem;
            font-weight: 600;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .version-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .version-item {
            display: inline-block;
            margin: 0.5rem 1rem;
            font-size: 1.1rem;
        }
        
        .version-label {
            font-weight: 500;
            opacity: 0.8;
        }
        
        .version-value {
            font-weight: 600;
            color: #4ecdc4;
        }
        
        .actions {
            margin: 2rem 0;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            margin: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #ff5252, #26d0ce);
            transform: translateY(-2px) scale(1.05);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .feature {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .feature-desc {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .footer {
            margin-top: 2rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Laravel 12</div>
        <div class="subtitle">High-Performance Web Application with Redis Integration</div>
        
        <div class="version-info">
            <div class="version-item">
                <span class="version-label">Laravel:</span>
                <span class="version-value">{{ app()->version() }}</span>
            </div>
            <div class="version-item">
                <span class="version-label">PHP:</span>
                <span class="version-value">{{ phpversion() }}</span>
            </div>
            <div class="version-item">
                <span class="version-label">Environment:</span>
                <span class="version-value">{{ app()->environment() }}</span>
            </div>
        </div>
        
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🚀</div>
                <div class="feature-title">High Performance</div>
                <div class="feature-desc">Optimized with Redis caching for lightning-fast responses</div>
            </div>
            <div class="feature">
                <div class="feature-icon">🔴</div>
                <div class="feature-title">Redis Integration</div>
                <div class="feature-desc">Full Redis support for caching, sessions, and data storage</div>
            </div>
            <div class="feature">
                <div class="feature-icon">🛡️</div>
                <div class="feature-title">Secure & Stable</div>
                <div class="feature-desc">Built with Laravel 12's latest security features</div>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <div class="feature-title">Monitoring Ready</div>
                <div class="feature-desc">Comprehensive dashboard and health checks</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="/dashboard" class="btn btn-primary">View Dashboard</a>
            <a href="/redis/test" class="btn">Test Redis</a>
            <a href="/api/health" class="btn">Health Check</a>
        </div>
        
        <div class="footer">
            <p>Built with ❤️ using Laravel 12 and Redis</p>
            <p>Current time: {{ now()->format('Y-m-d H:i:s T') }}</p>
        </div>
    </div>
</body>
</html>
