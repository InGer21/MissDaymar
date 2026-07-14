#!/usr/bin/env bash
set -e

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan view:cache

php artisan migrate --force 2>&1 || echo "⚠️ Migration failed — check logs"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
