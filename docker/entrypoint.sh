#!/bin/bash
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

chown -R www:www storage bootstrap/cache database || true

if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=.\+' .env; then
    php artisan key:generate --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link || true

exec "$@"
