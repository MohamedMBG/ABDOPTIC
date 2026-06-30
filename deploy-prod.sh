#!/usr/bin/env bash
# Run on the prod server after pulling new code. Makes the app fast.
# Prereqs (one-time, not in this script):
#   - .env has: APP_ENV=production, APP_DEBUG=false
#   - php.ini has OPcache on with opcache.validate_timestamps=0 (max speed)
set -e

composer install --no-dev --optimize-autoloader --classmap-authoritative

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear stale compiled PHP so the new deploy is actually served.
php -r 'function_exists("opcache_reset") && opcache_reset();' || true

echo "Deploy optimized. Restart php-fpm if opcache.validate_timestamps=0."
