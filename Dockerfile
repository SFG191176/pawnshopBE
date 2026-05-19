# ============================================
# STAGE 1: Install PHP dependencies via Composer
# ============================================
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts --no-interaction

# ============================================
# STAGE 2: Build Frontend Assets (Vite)
# ============================================
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --production=false
COPY . .
RUN npm run build

# ============================================
# STAGE 3: Production Runtime (PHP-FPM + Nginx)
# ============================================
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        bcmath \
        gd \
        opcache \
        pcntl \
        exif \
    && rm -rf /var/cache/apk/*

# Copy custom configs
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy vendor from composer stage
COPY --from=vendor /app/vendor ./vendor

# Copy built assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Copy entrypoint script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Create required directories and set permissions
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    storage/certs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Create nginx pid directory
RUN mkdir -p /run/nginx

# Expose port (Render assigns PORT env var)
EXPOSE 80

# Start via supervisor
CMD ["/usr/local/bin/start.sh"]
