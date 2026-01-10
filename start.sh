#!/bin/sh

# Default to port 80 if PORT var is not set
PORT=${PORT:-80}

# Replace "listen 80;" with "listen $PORT;" in the nginx config
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/conf.d/default.conf

echo "Starting Nginx on port $PORT..."

# Start Supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisor.conf
