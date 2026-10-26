# Production Dockerfile for Render & Containerized Deployments
FROM php:8.2-apache

# Install required database extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Working directory
WORKDIR /var/www/html

# Copy application source code
COPY src/ /var/www/html/

# Copy port configuration entrypoint script
COPY deploy/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]
