FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    libpq-dev \
    libzip-dev \
    unzip \
    zip \
    supervisor \
    curl && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

WORKDIR /var/www/html

COPY . .

RUN mkdir -p /run/php && \
    chown -R www-data:www-data /run/php && \
    sed -i 's/listen = 127.0.0.1:9000/listen = \/run\/php\/php-fpm.sock/g' /usr/local/etc/php-fpm.d/www.conf

COPY ./nginx.conf /etc/nginx/sites-available/default
COPY ./supervisor.conf /etc/supervisor/conf.d/supervisor.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n"]
