# ---- Build stage: install Composer deps ----
FROM composer:2 AS vendor
WORKDIR /app
# Ensure composer can extract archives and clone repos
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

# ---- App stage: PHP with Apache ----
FROM php:8.2-apache

# Install system deps and PHP extensions commonly needed by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
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
