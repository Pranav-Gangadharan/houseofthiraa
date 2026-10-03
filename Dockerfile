# syntax=docker/dockerfile:1

#
# ---- Stage 1: PHP dependencies ----
#
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-progress \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

#
# ---- Stage 2: front-end assets ----
#
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

#
# ---- Stage 3: runtime (php-fpm + nginx) ----
#
FROM php:8.3-fpm-alpine AS runtime

ARG APP_ENV=production

RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        sqlite \
        libpng \
        libjpeg-turbo \
        libwebp \
        libzip \
        freetype \
        icu-libs \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        freetype-dev \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        pdo_sqlite \
        gd \
        zip \
        intl \
        bcmath \
        opcache \
    && apk del .build-deps

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN addgroup -g 1000 www && adduser -G www -g www -s /bin/sh -D www \
    && chown -R www:www /var/www/html \
    && mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www:www storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENV APP_ENV=${APP_ENV} \
    LOG_CHANNEL=stderr

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
