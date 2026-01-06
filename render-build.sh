#!/usr/bin/env bash
# Exit on error
set -o errexit

npm install
npm run build

composer install --no-dev --optimize-autoloader --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Uncomment the following line to run migrations during deployment
# php artisan migrate --force
