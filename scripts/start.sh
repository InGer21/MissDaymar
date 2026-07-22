#!/usr/bin/env bash
set -e

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Fix log file permissions if it already exists
touch /var/www/html/storage/logs/laravel.log 2>/dev/null || true
chown www-data:www-data /var/www/html/storage/logs/laravel.log 2>/dev/null || true
chmod 664 /var/www/html/storage/logs/laravel.log 2>/dev/null || true

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan view:cache

php artisan migrate --force 2>&1 || echo "⚠️ Migration failed — check logs"

php artisan profit:import 2>&1 || echo "⚠️ Profit import failed — check logs"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
