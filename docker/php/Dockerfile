FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip postgresql-dev icu-dev oniguruma-dev linux-headers make gcc musl-dev \
    && docker-php-ext-install pdo_pgsql intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
