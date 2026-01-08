FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    pkg-config \
    libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY ./nginx.conf /etc/nginx/conf.d/default.conf

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD service nginx start && php-fpm
