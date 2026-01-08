FROM php:8.2-fpm

# Install Nginx and required system libraries
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    zip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Remove default nginx Welcome page
RUN rm -rf /usr/share/nginx/html/*

# Copy custom nginx configuration
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Start php-fpm and nginx when container runs
CMD service php-fpm start && nginx -g "daemon off;"
