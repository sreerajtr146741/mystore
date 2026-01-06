# Use the official PHP image with Apache
# Build: 2026-01-06 22:21 - Route fix applied
FROM php:8.2-apache


# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
RUN npm install && npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Update Apache configuration to point to public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Set ServerName to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache to use PORT environment variable
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf

# Update virtual host to use PORT variable
RUN sed -i 's/\*:80/*:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Configure php.ini for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== Starting Laravel Application ==="\n\
\n\
# Create .env file if it does not exist\n\
if [ ! -f .env ]; then\n\
    echo "Creating .env file..."\n\
    cat > .env << EOF\n\
APP_NAME="${APP_NAME:-Laravel}"\n\
APP_ENV="${APP_ENV:-production}"\n\
APP_KEY=\n\
APP_DEBUG="${APP_DEBUG:-false}"\n\
APP_URL="${APP_URL:-http://localhost}"\n\
\n\
LOG_CHANNEL=stack\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION="${DB_CONNECTION:-pgsql}"\n\
DB_HOST="${DB_HOST:-127.0.0.1}"\n\
DB_PORT="${DB_PORT:-5432}"\n\
DB_DATABASE="${DB_DATABASE:-laravel}"\n\
DB_USERNAME="${DB_USERNAME:-postgres}"\n\
DB_PASSWORD="${DB_PASSWORD:-}"\n\
\n\
BROADCAST_DRIVER=log\n\
CACHE_DRIVER=file\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=sync\n\
SESSION_DRIVER=file\n\
SESSION_LIFETIME=120\n\
\n\
MAIL_MAILER="${MAIL_MAILER:-smtp}"\n\
MAIL_HOST="${MAIL_HOST:-smtp.gmail.com}"\n\
MAIL_PORT="${MAIL_PORT:-587}"\n\
MAIL_USERNAME="${MAIL_USERNAME:-}"\n\
MAIL_PASSWORD="${MAIL_PASSWORD:-}"\n\
MAIL_ENCRYPTION="${MAIL_ENCRYPTION:-tls}"\n\
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"\n\
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Laravel}"\n\
EOF\n\
fi\n\
\n\
# Generate APP_KEY if not set in .env\n\
if ! grep -q "APP_KEY=base64:" .env; then\n\
    echo "Generating APP_KEY..."\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Clear any existing cache\n\
echo "Clearing cache..."\n\
php artisan config:clear || true\n\
php artisan route:clear || true\n\
php artisan view:clear || true\n\
\n\
# Cache configuration\n\
echo "Caching configuration..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Run migrations\n\
echo "Running migrations..."\n\
php artisan migrate --force || echo "Migration failed, continuing..."\n\
\n\
# Set proper permissions\n\
echo "Setting permissions..."\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\
# Show Laravel version\n\
echo "Laravel version: $(php artisan --version)"\n\
\n\
# Start Apache\n\
echo "Starting Apache..."\n\
exec apache2-foreground\n\
' > /usr/local/bin/docker-entrypoint.sh && chmod +x /usr/local/bin/docker-entrypoint.sh

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
