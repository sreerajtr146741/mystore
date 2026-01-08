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

# Copy supervisor configuration
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy start script and make executable
COPY ./start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Expose port (Render ignores this, but good for documentation)
EXPOSE 80

# Start Container using script to handle PORT
CMD ["/var/www/html/start.sh"]
