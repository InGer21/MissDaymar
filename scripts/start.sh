#!/usr/bin/env bash
set -e

# These run at container startup so they pick Render's env vars
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
