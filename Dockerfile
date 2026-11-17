# Construcci?n del frontend Vue y Vite
FROM node:24-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# Entorno principal de Laravel
FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
     && find /etc/apache2/mods-enabled -maxdepth 1 -name 'mpm_*.load' -delete \
    && find /etc/apache2/mods-enabled -maxdepth 1 -name 'mpm_*.conf' -delete \
    && a2enmod mpm_prefork rewrite headers \
    && apache2ctl -M | grep mpm_prefork_module \
    && ! apache2ctl -M | grep -E 'mpm_event_module|mpm_worker_module' \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build ./public/build

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf


RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache
