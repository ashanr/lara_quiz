# Laravel 12 Redis Application

This is a stable Laravel 12 application with Redis integration for caching, sessions, and queue management.

## Features

- Laravel 12.x (Latest stable version)
- Redis integration with Predis
- Docker Compose setup for easy development
- Redis-based caching and sessions
- Queue management with Redis
- RESTful API endpoints for Redis operations
- Comprehensive error handling

## Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js and NPM
- Docker and Docker Compose (for containerized setup)
- Redis server (included in Docker setup)

## Installation

### Option 1: Local Development

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start Redis server locally:**
   ```bash
   redis-server
   ```

5. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

6. **Build frontend assets:**
   ```bash
   npm run build
   ```

7. **Start the development server:**
   ```bash
   php artisan serve
   ```

### Option 2: Docker Development

1. **Copy environment file:**
   ```bash
   cp .env.sample .env
   php artisan key:generate
   ```

2. **Build and start services:**
   ```bash
   docker-compose up -d
   ```

2. **Install dependencies (if needed):**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install && npm run build
   ```

4. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

## Configuration

### Redis Configuration

The application is pre-configured to use Redis for:

- **Caching**: `CACHE_STORE=redis`
- **Sessions**: `SESSION_DRIVER=redis`
- **Queues**: `QUEUE_CONNECTION=redis`

### Environment Variables

Key Redis-related environment variables in `.env`:

```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

For Docker setup, Redis host should be `redis` (service name).

## API Endpoints

### Redis Test Endpoints

#### 1. Test Redis Connection
- **URL**: `GET /redis/test`
- **Description**: Tests Redis connection and displays system information
- **Response**:
  ```json
  {
    "status": "success",
    "redis_connection": "Connected",
    "test_value": "Hello from Redis!",
    "cached_value": "Cached value using Redis",
    "redis_version": "7.0.0",
    "used_memory_human": "1.20M",
    "connected_clients": "1"
  }
  ```

#### 2. Set Data in Redis
- **URL**: `POST /redis/set`
- **Parameters**:
  - `key` (string): Redis key
  - `value` (string): Value to store
  - `ttl` (integer): Time to live in seconds (default: 3600)
- **Example**:
  ```bash
  curl -X POST http://localhost:8000/redis/set \
    -H "Content-Type: application/json" \
    -d '{"key": "my_key", "value": "my_value", "ttl": 1800}'
  ```

#### 3. Get Data from Redis
- **URL**: `GET /redis/get/{key}`
- **Example**: `GET /redis/get/my_key`
- **Response**:
  ```json
  {
    "status": "success",
    "key": "my_key",
    "value": "my_value",
    "ttl": 1750
  }
  ```

#### 4. Delete Data from Redis
- **URL**: `DELETE /redis/delete/{key}`
- **Example**: `DELETE /redis/delete/my_key`

### API Routes

All endpoints are also available under `/api/` prefix:
- `GET /api/redis/test`
- `POST /api/redis/set`
- `GET /api/redis/get/{key}`
- `DELETE /api/redis/delete/{key}`

## Usage Examples

### Using Redis in Controllers

```php
use Illuminateupportacadesedis;
use Illuminateupportacadesache;

// Direct Redis usage
Redis::set('key', 'value');
$value = Redis::get('key');

// Using Laravel Cache (Redis backend)
Cache::put('key', 'value', 3600);
$value = Cache::get('key');

// Using Redis for sessions (automatic)
session(['user_id' => 123]);
$userId = session('user_id');
```

### Queue Jobs with Redis

1. **Create a job:**
   ```bash
   php artisan make:job ProcessOrder
   ```

2. **Dispatch the job:**
   ```php
   ProcessOrder::dispatch($orderData);
   ```

3. **Run queue worker:**
   ```bash
   php artisan queue:work redis
   ```

## Monitoring Redis

### Using Redis CLI

Connect to Redis and monitor:
```bash
redis-cli monitor
```

### Laravel Commands

Check queue status:
```bash
php artisan queue:work redis --verbose
php artisan queue:failed
```

Clear cache:
```bash
php artisan cache:clear
```

## Performance Optimization

### Redis Configuration

For production, consider these Redis configurations:

```ini
# redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### Laravel Optimization

1. **Config caching:**
   ```bash
   php artisan config:cache
   ```

2. **Route caching:**
   ```bash
   php artisan route:cache
   ```

3. **View caching:**
   ```bash
   php artisan view:cache
   ```

## Testing

### Run PHPUnit tests:
```bash
php artisan test
```

### Test Redis connectivity:
```bash
curl http://localhost:8000/redis/test
```

## Troubleshooting

### Common Issues

1. **Redis connection failed:**
   - Check if Redis server is running
   - Verify Redis host and port in `.env`
   - Check firewall settings

2. **Permission errors:**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

3. **Clear application cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Docker Issues

1. **Rebuild containers:**
   ```bash
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

2. **Check logs:**
   ```bash
   docker-compose logs redis
   docker-compose logs app
   ```

## Security Considerations

1. **Redis Authentication:**
   - Set Redis password in production
   - Use Redis AUTH command
   - Configure firewall rules

2. **Environment Security:**
   - Never commit `.env` file
   - Use strong APP_KEY
   - Secure Redis network access

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This Laravel application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions:
- Check the [Laravel Documentation](https://laravel.com/docs)
- Check the [Redis Documentation](https://redis.io/documentation)
- Review the troubleshooting section above

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
