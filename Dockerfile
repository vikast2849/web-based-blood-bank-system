# Production Dockerfile for Render & Containerized Deployments
FROM php:8.2-apache

# Install MariaDB server, client, and required PHP extensions
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
        mariadb-server \
        mariadb-client && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Optimize MariaDB configuration for low-memory container usage (~30MB)
RUN printf "[mysqld]\nperformance_schema = OFF\ninnodb_buffer_pool_size = 16M\ninnodb_log_buffer_size = 1M\nkey_buffer_size = 8M\nmax_connections = 25\nbind-address = 127.0.0.1\n" > /etc/mysql/mariadb.conf.d/99-low-memory.cnf

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy custom Apache virtual host configuration with explicit permissions
COPY deploy/apache-default.conf /etc/apache2/sites-available/000-default.conf

# Working directory
WORKDIR /var/www/html

# Copy database schema and initial seed data
COPY database/ /var/www/database/

# Copy application source code into web root
COPY src/ /var/www/html/

# Copy port configuration and entrypoint script
COPY deploy/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure file ownership and permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose HTTP ports
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]
