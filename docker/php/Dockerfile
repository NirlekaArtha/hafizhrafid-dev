FROM docker.io/library/php:8.5-fpm-alpine

RUN apk add --no-cache \
    git curl libpng-dev libzip-dev icu-dev postgresql-dev linux-headers $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql pgsql gd zip intl bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=docker.io/library/composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
