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

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Remove default nginx page
RUN rm -rf /usr/share/nginx/html/*

# Copy nginx config
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 80

# Start Supervisor (runs PHP-FPM + Nginx)
CMD ["/usr/bin/supervisord"]
