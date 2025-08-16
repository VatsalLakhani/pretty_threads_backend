# ---- Build stage: install Composer deps ----
FROM composer:2 AS vendor
WORKDIR /app
# Ensure composer can extract archives and clone repos (Alpine base)
RUN apk add --no-cache unzip git
ENV COMPOSER_MEMORY_LIMIT=-1 \
    COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-req=ext-*

# ---- App stage: PHP with Apache ----
FROM php:8.2-apache

# Install system deps and PHP extensions commonly needed by Laravel
# Install system deps and PHP extensions commonly needed by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        zlib1g-dev \
        unzip \
        git \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        bcmath \
        mbstring \
    && a2enmod rewrite \
    && sh -lc 'echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf' \
    && a2enconf servername \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app source
COPY . /var/www/html

# Copy vendor from build stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Ensure storage and bootstrap cache are writable
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Set Apache DocumentRoot to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Add entrypoint to bind Apache to $PORT and run Laravel optimizations
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Railway uses $PORT; default to 8080 locally
ENV PORT=8080

EXPOSE 8080
CMD ["/entrypoint.sh"]
