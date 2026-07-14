#!/usr/bin/env bash
set -e

php artisan migrate --force --isolated || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
