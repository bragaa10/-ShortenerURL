FROM php:8.4-apache

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . .

# Ensure runtime and assets directories exist for Yii2
RUN mkdir -p runtime web/assets && \
    chmod -R 777 runtime web/assets

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod +x docker-entrypoint.sh

# Enable Apache modules
RUN a2enmod rewrite

# Configure Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Use entrypoint script to run migrations
ENTRYPOINT ["./docker-entrypoint.sh"]