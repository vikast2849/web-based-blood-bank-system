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

# Prepare runtime directory for MariaDB socket
mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

# Start MariaDB service for local self-contained database support
echo "Starting MariaDB service..."
service mariadb start || /etc/init.d/mariadb start

# Wait up to 15 seconds for MariaDB to become ready
echo "Waiting for MariaDB to accept connections..."
for i in {1..15}; do
    if mysqladmin ping -u root --silent 2>/dev/null; then
        echo "MariaDB is ready."
        break
    fi
    sleep 1
done

# Initialize database schema and seed data
echo "Checking database initialization..."
mariadb -u root -e "CREATE DATABASE IF NOT EXISTS bloodbank DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true

TABLE_COUNT=$(mariadb -u root -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'bloodbank';" 2>/dev/null || echo "0")
if [ "$TABLE_COUNT" -eq "0" ]; then
    echo "Importing initial bloodbank database schema and seed data..."
    if [ -f /var/www/database/bloodbank.sql ]; then
        mariadb -u root bloodbank < /var/www/database/bloodbank.sql
        echo "Imported /var/www/database/bloodbank.sql successfully."
    elif [ -f /var/www/database/schema.sql ]; then
        mariadb -u root bloodbank < /var/www/database/schema.sql
        echo "Imported /var/www/database/schema.sql successfully."
    fi
else
    echo "Database bloodbank already contains ${TABLE_COUNT} tables."
fi

# Configure authentication plugins and permissions so PHP (www-data) can connect via TCP
echo "Configuring MariaDB user privileges..."
mariadb -u root <<'EOF' || true
ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
ALTER USER 'root'@'127.0.0.1' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
CREATE USER IF NOT EXISTS 'bloodbank'@'localhost' IDENTIFIED BY 'bloodbank';
ALTER USER 'bloodbank'@'localhost' IDENTIFIED BY 'bloodbank';
CREATE USER IF NOT EXISTS 'bloodbank'@'127.0.0.1' IDENTIFIED BY 'bloodbank';
ALTER USER 'bloodbank'@'127.0.0.1' IDENTIFIED BY 'bloodbank';
CREATE USER IF NOT EXISTS 'bloodbank'@'%' IDENTIFIED BY 'bloodbank';
ALTER USER 'bloodbank'@'%' IDENTIFIED BY 'bloodbank';
GRANT ALL PRIVILEGES ON bloodbank.* TO 'bloodbank'@'localhost';
GRANT ALL PRIVILEGES ON bloodbank.* TO 'bloodbank'@'127.0.0.1';
GRANT ALL PRIVILEGES ON bloodbank.* TO 'bloodbank'@'%';
FLUSH PRIVILEGES;
EOF

echo "MariaDB is ready. Starting Apache in foreground on port ${TARGET_PORT}..."
exec apache2-foreground
