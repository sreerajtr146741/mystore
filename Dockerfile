FROM php:8.2-fpm

# Install Nginx + system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    zip \
    supervisor

# Install PDO extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Remove default nginx page and configs
RUN rm -rf /usr/share/nginx/html/* \
    && rm -rf /etc/nginx/sites-enabled/* \
    && rm -rf /etc/nginx/sites-available/*

# Copy nginx config
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy start script and make executable
COPY ./start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Expose port (Render ignores this, but good for documentation)
EXPOSE 80

# Start Container using script to handle PORT
CMD ["/var/www/html/start.sh"]
