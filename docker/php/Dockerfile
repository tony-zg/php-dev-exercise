FROM composer:2.8.12 AS composer
FROM php:8.4.13-fpm-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libonig-dev libzip-dev libxml2-dev libsqlite3-dev unzip git \
    && docker-php-ext-install -j$(nproc) intl mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/development.ini /usr/local/etc/php/conf.d/development.ini
WORKDIR /var/www/html
