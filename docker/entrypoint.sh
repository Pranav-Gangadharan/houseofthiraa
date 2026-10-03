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

# Seed the sample catalogue only when the store is empty, so admin edits are never overwritten.
if [ "$(php artisan tinker --execute='echo App\Models\Product::count();' 2>/dev/null | tail -n1 | tr -d '[:space:]')" = "0" ]; then
    php artisan db:seed --force
fi
php artisan storage:link || true

exec "$@"
