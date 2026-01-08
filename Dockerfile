FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx

WORKDIR /var/www/html

# Copy project files
COPY . .

# Remove default nginx Welcome page
RUN rm -rf /usr/share/nginx/html/*

# Copy custom nginx configuration
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Start php-fpm + nginx
CMD service php-fpm start && nginx -g "daemon off;"
