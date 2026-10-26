#!/usr/bin/env bash
# Deploy Blood Bank on Amazon Linux 2023 with Nginx + PHP-FPM + MariaDB.
# Usage:  sudo bash deploy/ec2-nginx-setup.sh <domain> [letsencrypt-email]
# Example: sudo bash deploy/ec2-nginx-setup.sh bloodbank.vikasst.shop me@example.com
set -euo pipefail

DOMAIN="${1:?Usage: sudo bash deploy/ec2-nginx-setup.sh <domain> [email]}"
EMAIL="${2:-}"
REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
WEB_ROOT=/var/www/bloodbank
DB_NAME=bloodbank
DB_USER=bloodbank

echo "=== Blood Bank EC2 Nginx Deployment for $DOMAIN ==="

# 1. Install PHP, PHP-FPM, MySQL extension, and MariaDB
echo "[1/6] Installing packages (php, php-fpm, php-mysqlnd, mariadb105-server)..."
dnf install -y php8.3 php8.3-fpm php8.3-mysqlnd mariadb105-server

# 2. Configure, Enable & Start Services
echo "[2/6] Configuring and starting MariaDB and PHP-FPM..."
sed -i 's/^user = apache/user = nginx/' /etc/php-fpm.d/www.conf || true
sed -i 's/^group = apache/group = nginx/' /etc/php-fpm.d/www.conf || true
systemctl enable --now mariadb
systemctl enable --now php-fpm
systemctl restart php-fpm

# 3. Deploy Web Files
echo "[3/6] Deploying application to $WEB_ROOT..."
mkdir -p "$WEB_ROOT"
rm -rf "$WEB_ROOT/src" "$WEB_ROOT/database" "$WEB_ROOT/tools" "$WEB_ROOT/docs" "$WEB_ROOT/tests"
cp -r "$REPO_DIR/src" "$REPO_DIR/database" "$REPO_DIR/tools" "$REPO_DIR/docs" "$REPO_DIR/tests" "$WEB_ROOT/"

# 4. Database Setup & User Provisioning
echo "[4/6] Setting up database..."
DB_PASS="$(openssl rand -hex 16)"
mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4;"
if [ "$(mysql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME' AND table_name='hospitals'")" = "0" ]; then
  mysql < "$WEB_ROOT/database/bloodbank.sql"
fi
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; GRANT ALL ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

# Write local config
cat > "$WEB_ROOT/src/file/config.local.php" <<PHP
<?php
return ['host' => 'localhost', 'user' => '$DB_USER', 'pass' => '$DB_PASS', 'name' => '$DB_NAME'];
PHP
chmod 640 "$WEB_ROOT/src/file/config.local.php"
chown -R nginx:nginx "$WEB_ROOT"

# 5. Configure Nginx Server Block (independent from existing sites)
echo "[5/6] Configuring Nginx server block for $DOMAIN..."
cat > "/etc/nginx/conf.d/bloodbank.conf" <<CONF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;

    root $WEB_ROOT/src;
    index index.php index.html;

    # Deny access to local config file
    location ~* /config\.local\.php\$ {
        deny all;
        return 404;
    }

    # Deny hidden files
    location ~ /\. {
        deny all;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php\$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|webp)\$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
CONF

nginx -t
systemctl reload nginx

# 6. Certbot SSL Configuration (if email provided and DNS resolves)
echo "[6/6] Checking SSL configuration..."
if [ -n "$EMAIL" ]; then
  if host "$DOMAIN" >/dev/null 2>&1; then
    echo "Running Certbot for $DOMAIN..."
    certbot --nginx -d "$DOMAIN" -m "$EMAIL" --agree-tos --non-interactive --redirect || echo "Certbot note: Could not issue cert automatically. Will remain on HTTP until DNS resolves."
  else
    echo "Notice: DNS for $DOMAIN has not propagated yet. Add the DNS A record pointing to this server's IP, then run:"
    echo "  sudo certbot --nginx -d $DOMAIN -m $EMAIL --agree-tos --redirect"
  fi
fi

echo ""
echo "=== Deployment Complete ==="
echo "DocumentRoot: $WEB_ROOT/src"
echo "URL: http://$DOMAIN/ (or https://$DOMAIN/ once DNS & SSL are configured)"
