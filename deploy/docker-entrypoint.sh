#!/usr/bin/env bash
set -e

# Render dynamically assigns a port via the PORT environment variable (typically 10000).
# Fall back to 80 if PORT is not set.
TARGET_PORT="${PORT:-80}"

echo "Configuring Apache to listen on port ${TARGET_PORT}..."
sed -i "s/Listen 80/Listen ${TARGET_PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${TARGET_PORT}/g" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
