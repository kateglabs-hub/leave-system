# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files first
COPY . .

# Set proper permissions BEFORE copying Apache config
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy Apache configuration
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable required Apache modules
RUN a2enmod headers && a2enmod rewrite && a2enmod expires && a2enmod deflate

# Set PHP configuration for development
RUN echo "display_errors = On" > /usr/local/etc/php/conf.d/docker.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker.ini \
    && echo "error_log = /var/log/apache2/php-error.log" >> /usr/local/etc/php/conf.d/docker.ini

# Create PHP error log and set permissions
RUN touch /var/log/apache2/php-error.log && chown www-data:www-data /var/log/apache2/php-error.log

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=10s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
