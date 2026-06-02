# syntax=docker/dockerfile:1

# -----------------------------------------------------------------------------
# Stage 1: Build frontend assets (Vite + React + Inertia)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js jsconfig.json ./
COPY resources ./resources

RUN npm run build

# -----------------------------------------------------------------------------
# Stage 2: Install PHP dependencies (PHP 8.2 — matches composer.json ^8.1)
# -----------------------------------------------------------------------------
FROM php:8.2-cli-alpine AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache git unzip

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative

# -----------------------------------------------------------------------------
# Stage 3: Production runtime (PHP-FPM)
# -----------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="card-nexus"
LABEL org.opencontainers.image.description="Card Nexus - Laravel 10 + Inertia"

RUN apk add --no-cache \
    bash \
    curl \
    fcgi \
    libzip-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

COPY docker/php/conf.d/laravel.ini /usr/local/etc/php/conf.d/99-laravel.ini

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=frontend --chown=www-data:www-data /app/public/build /var/www/html/public/build

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]

# -----------------------------------------------------------------------------
# Stage 5: Nginx with built public assets
# -----------------------------------------------------------------------------
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=production /var/www/html/public /var/www/html/public

EXPOSE 80

# -----------------------------------------------------------------------------
# Stage 4: Development image (bind-mount friendly)
# -----------------------------------------------------------------------------
FROM production AS development

USER root

RUN apk add --no-cache nodejs npm \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER www-data
