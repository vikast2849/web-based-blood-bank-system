# Production Dockerfile for Render & Containerized Deployments
FROM php:8.2-apache

# Install required database extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy custom Apache virtual host configuration with explicit permissions
COPY deploy/apache-default.conf /etc/apache2/sites-available/000-default.conf

# Working directory
WORKDIR /var/www/html

# Copy application source code into web root
COPY src/ /var/www/html/

# Copy port configuration entrypoint script
COPY deploy/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure file ownership and permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose HTTP ports
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]
