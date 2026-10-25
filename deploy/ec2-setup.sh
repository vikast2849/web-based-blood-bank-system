#!/usr/bin/env bash
# Deploy Blood Bank on Amazon Linux 2023 (Apache + PHP + MariaDB).
# Usage:  sudo bash deploy/ec2-setup.sh <domain> [letsencrypt-email]
# Example: sudo bash deploy/ec2-setup.sh bloodbank.example.com me@example.com
set -euo pipefail

DOMAIN="${1:?Usage: sudo bash deploy/ec2-setup.sh <domain> [email]}"
EMAIL="${2:-}"
REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
WEB_ROOT=/var/www/bloodbank
DB_NAME=bloodbank
DB_USER=bloodbank

if systemctl is-active --quiet nginx; then
  echo "nginx is already running on this server. This script configures Apache only."
  echo "Add a server block for $DOMAIN pointing at $WEB_ROOT/src instead (see README)."
  exit 1
fi

dnf install -y httpd php8.2 php8.2-mysqlnd mariadb105-server
systemctl enable --now mariadb httpd

mkdir -p "$WEB_ROOT"
rm -rf "$WEB_ROOT/src" "$WEB_ROOT/database"
cp -r "$REPO_DIR/src" "$REPO_DIR/database" "$WEB_ROOT/"

# Database + dedicated user (random password, stored only in config.local.php)
DB_PASS="$(openssl rand -hex 16)"
mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4;"
if [ "$(mysql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME' AND table_name='hospitals'")" = "0" ]; then
  mysql < "$WEB_ROOT/database/bloodbank.sql"
fi
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; GRANT ALL ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

cat > "$WEB_ROOT/src/file/config.local.php" <<PHP
<?php
return ['host' => 'localhost', 'user' => '$DB_USER', 'pass' => '$DB_PASS', 'name' => '$DB_NAME'];
PHP
chmod 640 "$WEB_ROOT/src/file/config.local.php"
chown -R apache:apache "$WEB_ROOT"

# Virtual host: the site root IS the login page (index.php redirects to login.php)
cat > /etc/httpd/conf.d/bloodbank.conf <<CONF
<VirtualHost *:80>
    ServerName $DOMAIN
    DocumentRoot $WEB_ROOT/src
    DirectoryIndex index.php
    <Directory $WEB_ROOT/src>
        Require all granted
        Options -Indexes
    </Directory>
    <FilesMatch "^config\.local\.php$">
        Require all denied
    </FilesMatch>
</VirtualHost>
CONF

apachectl configtest
systemctl restart httpd php-fpm 2>/dev/null || systemctl restart httpd

if [ -n "$EMAIL" ]; then
  dnf install -y certbot python3-certbot-apache \
    && certbot --apache -d "$DOMAIN" -m "$EMAIL" --agree-tos --non-interactive --redirect \
    || echo "Certbot step failed - the site still works over http://$DOMAIN"
fi

echo
echo "Done. Open: http://$DOMAIN/  (it lands on the login page)"
echo "Make sure the DNS A record for $DOMAIN points to this server and ports 80/443 are open."
