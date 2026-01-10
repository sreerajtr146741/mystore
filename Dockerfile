# -------------------------------
# 1. Base PHP Image with FPM
# -------------------------------
FROM php:8.2-fpm

# -------------------------------
# 2. Install System Libraries
# -------------------------------
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zip \
    supervisor \
    curl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# -------------------------------
# 3. Install Composer
# -------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------
# 4. Set Working Directory
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# 5. Copy Laravel Application
# -------------------------------
COPY . .

# -------------------------------
# 6. Install PHP Dependencies
# -------------------------------
RUN composer install --no-dev --optimize-autoloader --no-progress --no-interaction

# -------------------------------
# 6b. Configure PHP-FPM for Socket
# -------------------------------
RUN mkdir -p /run/php \
    && chown -R www-data:www-data /run/php \
    && sed -i 's/listen = 127.0.0.1:9000/listen = \/run\/php\/php-fpm.sock/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/listen = 9000/listen = \/run\/php\/php-fpm.sock/g' /usr/local/etc/php-fpm.d/zz-docker.conf || true \
    && echo "listen.owner = www-data" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "listen.group = www-data" >> /usr/local/etc/php-fpm.d/www.conf

# -------------------------------
# 7. Set Proper Permissions
# -------------------------------
RUN chmod -R 775 storage bootstrap/cache

# -------------------------------
# 8. Copy Nginx Configuration
# -------------------------------
COPY ./nginx.conf /etc/nginx/sites-available/default

# -------------------------------
# 9. Copy Supervisor Config
# -------------------------------
COPY ./supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# -------------------------------
# 10. Expose Container Port
# -------------------------------
EXPOSE 80

# -------------------------------
# 11. Start Supervisor (runs nginx + php-fpm)
# -------------------------------
CMD ["/usr/bin/supervisord", "-n"]
