# ---------------------------------------------------
# PHP + Nginx + Composer Dockerfile for Laravel 10/11
# ---------------------------------------------------

# PHP 8.3 FPM
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy Nginx configuration
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Laravel storage permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 80

# Start Nginx + PHP-FPM
CMD service nginx start && php-fpm
