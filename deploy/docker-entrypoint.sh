#!/usr/bin/env bash
set -e

# Render assigns dynamic port via $PORT (e.g. 10000). Default to 80 if not set.
TARGET_PORT="${PORT:-80}"

echo "Configuring Apache to listen on port ${TARGET_PORT}..."
sed -i "s/Listen 80/Listen ${TARGET_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${TARGET_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure permissions on web root
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

exec apache2-foreground
