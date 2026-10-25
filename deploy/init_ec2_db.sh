#!/usr/bin/env bash
set -euo pipefail

DB_NAME=bloodbank
DB_USER=bloodbank
DB_PASS="$(openssl rand -hex 16)"

echo "[1/4] Creating database '$DB_NAME'..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4;"

echo "[2/4] Importing schema and seed data..."
mysql "$DB_NAME" < /var/www/bloodbank/database/bloodbank.sql

echo "[3/4] Creating user '$DB_USER'..."
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; GRANT ALL ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

echo "[4/4] Writing local configuration file..."
cat > /var/www/bloodbank/src/file/config.local.php <<PHP
<?php
return [
    'host' => 'localhost',
    'user' => '$DB_USER',
    'pass' => '$DB_PASS',
    'name' => '$DB_NAME'
];
PHP

chmod 640 /var/www/bloodbank/src/file/config.local.php
chown nginx:nginx /var/www/bloodbank/src/file/config.local.php

echo "=== DATABASE INITIALIZATION SUCCESSFUL ==="
